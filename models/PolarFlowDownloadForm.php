<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * PolarFlowDownloadForm is the model behind the download form.
 */
class PolarFlowDownloadForm extends Model
{
    public $email;
    public $password;
    public $datefrom;
    public $dateto;
    public $saveTrainingList = false;
    public $zip = 'Y';

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email'],     'email'],
            [['password'],  'string'],
            [['datefrom',   'dateto'], 'string', 'min'=>10, 'max'=>10],
            [['zip'],       'in', 'range' => ['Y', 'N']],
            [['saveTrainingList'], 'boolean'],
        ];
    }  

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Polar Flow Email'),
            'password' => Yii::t('app', 'Polar Flow Password'),
            'datefrom' => Yii::t('app', 'Download Tracks from Date'),
            'dateto' => Yii::t('app', 'Download Tracks to Date'),
            'saveTrainingList' => Yii::t('app', 'Save Training List'),
        ];
    }

    public function download($userId)
    {
        ob_start();
        $tcx=$zip='';
        $dir = Yii::getAlias('@data/uploads/temp/'.$userId);
        // Create export dir?
        if (!is_dir($dir)) {
            mkdir($dir, 0755);
            echo "Export directory ".$dir." created"."\n";
        } else {
            echo "Export directory ".$dir." already exists"."\n";
        }
        $cookiejar = Yii::getAlias('@data/uploads/temp/CookieJar_'.$userId.'.txt');

        $ch = curl_init();

        echo 'GET ajaxLogin...';

        curl_setopt($ch, CURLOPT_URL,               'https://flow.polar.com/ajaxLogin');
        curl_setopt($ch, CURLOPT_COOKIEJAR,         $cookiejar);
        curl_setopt($ch, CURLOPT_COOKIEFILE,        $cookiejar);
        curl_setopt($ch, CURLOPT_USERAGENT,         'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_REFERER,           'https://flow.polar.com/');
        curl_setopt($ch, CURLOPT_AUTOREFERER,       true);
        // Activate this to get verbose output:
        // curl_setopt($ch, CURLOPT_VERBOSE,        true);

        $arr = curl_exec($ch); //get login page
        if($arr!==false)
            echo 'done'."\n";
        else {
            echo 'Error!'."\n";
            exit(1);
        }
        // DEBUG
        // echo "Result:"."\n";
        // echo $arr;

        // Get CSRF Token
        // <input type="hidden" name="csrfToken" value="3aeda..."/>
        echo "Extract CSRF token...";
        if (preg_match("/name=\"csrfToken\" value=\"([a-zA-z0-9-]+)\"/", $arr, $csrfToken)) {
            // Token is in $csrfToken[1];
            echo "done"."\n";
        } else {
            echo "Error! Cannnot get CSRF token."."\n";
            exit(1);
        }

        echo 'POST login...';
        $post_fields = 'returnUrl=%2F&email=' . $this->email . '&password=' . $this->password . '&csrfToken='.$csrfToken[1];

        curl_setopt($ch, CURLOPT_URL, 'https://flow.polar.com/login');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

        $arr = curl_exec($ch); //post credentials
        echo 'done'."\n";
        // DEBUG print_r($arr);

        echo 'GET training/getCalendarEvents...';
        curl_setopt($ch, CURLOPT_URL, 'https://flow.polar.com/training/getCalendarEvents?start=' . date('d.m.Y', strtotime($this->datefrom)) . '&end=' . date('d.m.Y', strtotime($this->dateto)));
        curl_setopt($ch, CURLOPT_POST, 0);

        // Get activity list
        $arr = curl_exec($ch); 
        echo 'done'."\n";
        // DEBUG print_r($arr);
        if($this->saveTrainingList) {
            echo 'Saving training list as JSON...';
            $fp = fopen($dir.'/'.'trainings_list_'.date('Y-m-d_His').'.json', "w+");
            fwrite($fp, $arr);
            fclose($fp);
            echo 'done'."\n";
        }

        $activity_arr = json_decode($arr);
        $counter = count($activity_arr); 
        // DEBUG print_r($activity_arr);

        if ($counter == 0) {
            echo "No trainings available, exiting";
            exit(0);
        }

        echo $counter." trainings/fitness data found between " . $this->datefrom . " and " . $this->dateto . "\n";

        $counter=0;
        // Count EXERCISE records
        foreach ($activity_arr as $activity) {
            if ($activity->type == 'EXERCISE') { 
                //echo $activity->type." ".$activity->listItemId." ".$activity->iconUrl." ".$activity->datetime."\n";
                $counter++;
            }
        }

        $tz_fix_offset = '-02:00';
        $count = 1;

        foreach ($activity_arr as $activity) {
            if ($activity->type !== 'EXERCISE') 
                continue;

            // Export pure tcx files       
            $tcxurl = 'https://flow.polar.com/api/export/training/tcx/' . $activity->listItemId;  
            $tcxname =  $dir.'/'.$this->email . '-'. $activity->datetime . '.tcx';	 

            if ( $this->zip == 'Y' ){ 	
                // Export Zipped files
                $tcxurl = 'https://flow.polar.com/api/export/training/tcx/' . $activity->listItemId . '?compress=true';   
                $tcxname = $dir.'/'.$this->email . '-' . $activity->datetime . '.zip';
            }
            echo 'GET export/training URL: ' . $tcxurl . "...";                                                                                                                                                   
            curl_setopt($ch, CURLOPT_URL, $tcxurl);
            // DEBUG print_r($tcx);

            $tcxname =  $dir.'/'.str_replace('@', '__at__', $this->email).'-'. str_replace(':', '_', $activity->datetime) . '.zip';
            $tcxname = utf8_decode($tcxname);
            echo "Saving file to: ".$tcxname.'...';

            //Open file handler.
            $fp = @fopen($tcxname, 'w+');
            //If $fp is FALSE, something went wrong.
            if($fp === false){
                throw new Exception('Could not open: ' . $tcxname);
            }
            //Pass our file handle to cURL.
            curl_setopt($ch, CURLOPT_FILE, $fp);

            //Timeout if the file doesn't download after 20 seconds.
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            $fixedtcx = str_replace('000Z', '000' . $tz_fix_offset, $tcx);                                                                                                              

            //Execute the request.
            curl_exec($ch);

            //If there was an error, throw an Exception
            if(curl_errno($ch)){
                throw new Exception(curl_error($ch));
            }

            //Get the HTTP status code.
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //Close the cURL handler.
            // curl_close($ch);

            //Close the file handler.
            fclose($fp);

            if($statusCode == 200){
                echo 'downloaded'."\n";
            } else{
                echo "Download failed, Status Code: " . $statusCode."\n";
            }

            // unZip flow file
            if ($this->zip == 'Y') {
                echo "Unzip file ".basename($tcxname).'...';
                $zip = new \ZipArchive;
                $res = $zip->open($tcxname);
                if ($res === TRUE) {
                    $zip->extractTo($dir.'/');
                    $statIndex = $zip->statIndex(0);
                    $zip->close();
                }
                unlink($tcxname);
                $tcxname = $dir.'/'.$statIndex['name'];
                echo 'done'."\n";

            }
            echo "File ".$count." of ".$counter." at https://flow.polar.com".$activity->url." exported\n";                                                                                                                                                                          
            // Create track record
            $model = new Track();

            // file is uploaded successfully
            $model->filenames   = $tcxname;
            $model->xml         = file_get_contents($tcxname);
            $model->filename    = $tcxname;
            // Read XML
            $xml                = simplexml_load_string($model->xml);
            $model->trackId     = (string)$xml->Activities->Activity[0]->Id;
            $model->sport       = (string)$xml->Activities->Activity[0]['Sport'];

            if ( /* $model->load($this->request->post()) && */ $model->save()) {
                echo 'Track record created: #' . $model->id . "\n";
                $model->addFiles();
            } else {
                echo 'Error while creating new track'."\n";
                \yii\helpers\VarDumper::dump($model->errors);
                echo "\n";
            }
            $count++;
        }
        // close curl resource to free up system resources
        curl_close($ch);     

        // Delete cookie jar
        // unlink($cookiejar);

        return ob_get_clean();
    }
}
