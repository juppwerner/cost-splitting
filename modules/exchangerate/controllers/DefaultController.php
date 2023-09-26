<?php

namespace app\modules\exchangerate\controllers;

use app\modules\exchangerate\models\Exchangerate;
use app\modules\exchangerate\models\forms\UploadEzbDataForm;
use app\modules\exchangerate\models\search\ExchangerateSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for Exchangerate model.
 */
class DefaultController extends Controller
{
    public $ezbLink = 'https://www.ecb.europa.eu/rss/fxref-CURRENCY.html';

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Exchangerate models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ExchangerateSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApi($currencyCode, $date=null)
    {
        $this->layout = false;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $allowedCurrencies = [
            'USD',
            'JPYi',
            'BGN',
            'CZK',
            'DKK',
            'EEK',
            'GBP',
            'HUF',
            'PLN',
            'RON',
            'SEK',
            'CHF',
            'ISK',
            'NOK',
            'RUB',
            'TRY',
            'AUD',
            'BRL',
            'CAD',
            'CNY',
            'HKD',
            'IDR',
            'INR',
            'KRW',
            'MXN',
            'MYR',
            'NZD',
            'PHP',
            'SGD',
            'THB',
            'ZAR',
        ];

        if(!in_array($currencyCode, $allowedCurrencies))
            throw new NotFoundHttpException(Yii::t('exchangerate', 'The requested currency is not allowed.'));
    
        if(empty($date) || $date == date('Y-m-d')) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }

        $model = Exchangerate::find()
            ->where(['currencyCode' => $currencyCode])
            ->andWhere("histDate <= '" .$date . "'")
            ->orderBy('histDate DESC')  
            ->one();
        if(!empty($model)) {
            return [
                'histDate'=>$model->histDate, 
                'currencyCode' => $currencyCode,
                'exchangeRate' => $model->exchangeRate
            ];
        }
        $diff = date_diff(date_create($date), date_create(date('Y-m-d', time())));
        // return $diff->format('%a');
        if($diff->format('%a')<=5+2) {
            // Call rdf from EZB
            $rawXML = file_get_contents(str_replace('CURRENCY', strtolower($currencyCode), $this->ezbLink));
            $xml = new \SimpleXMLElement($rawXML);
            $xml->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

            $items = $xml->item;
            foreach($items as $item) {
                // echo $item->title;
                if(preg_match('/^([-+]?[0-9]*\.?[0-9]+) ([A-Z]{3}) = 1 EUR ([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/', $item->title, $matches)) {
                    print_r($matches);
                    $model = new Exchangerate();
                    $model->histDate = $matches[3];  
                    $model->currencyCode = $currencyCode;
                    $model->exchangeRate = $matches[1];
                    // if(!$model->save())
                    //     print_r($model->errors);
                    if($date==$model->histDate) {
                        return [
                            'histDate'=>$model->histDate, 
                            'currencyCode' => $currencyCode,
                            'exchangeRate' => $model->exchangeRate
                        ];
                    }
                }
            }
        }
        // return \yii\helpers\VarDumper::dumpAsString($model->attributes, 10, true);
    }

    public function actionImport()
    {
        $model = new UploadEzbDataForm();
        if (Yii::$app->request->isPost && $model->load($this->request->post())) {
            $model->csvFile = UploadedFile::getInstance($model, 'csvFile');
            if ($model->upload()) {
                // file is uploaded successfully
                $csvFilePath = $model->uploadPath;
                // Trucate table requested?
                if($model->truncateTable) {
                    Yii::$app->db->createCommand('TRUNCATE TABLE {{%exchangerate}}')->execute();
                }
                // Check if the file exists
                if (file_exists($csvFilePath)) {
                    // Open the CSV file for reading
                    $file = fopen($csvFilePath, 'r');
        
                    if ($file) {
                        // Initialize an array to store the CSV data
                        $csvData = [];
                        $nR = 0;
                        $headers = [];
                        $fields = [];
                        // Read each line of the CSV file and parse it
                        while (($row = fgetcsv($file)) !== false) {
                            // $row is now an array containing the values of the current row
                            // You can access specific columns using $row[index], where index is the column number (0-based)
                            if($nR == 0) {
                                $headers = $row;
                                $nR++;
                                continue;
                            }
                            // Add the current row to the CSV data array
                            $csvData[] = $row;
                            $rows = [];
                            for($i=1; $i<count($headers); $i++) {
                                if($row[$i]==='N/A')
                                    continue;
                                if(trim($headers[$i])==='' || trim($row[$i])==='')
                                    continue;
                                $rows[] = [
                                    'histDate' => $row[0],
                                    'currencyCode' => $headers[$i],
                                    'exchangeRate' => $row[$i]
                                ];
                            }
                            // \yii\helpers\VarDumper::dump($rows, 10, true);
                            Yii::$app->db->createCommand()->batchInsert('{{%exchangerate}}', ['histDate', 'currencyCode', 'exchangeRate'], $rows)->execute();
                            $nR++;
                        }
                        // Close the CSV file
                        fclose($file);
                        // Now $csvData contains all the rows from the CSV file
                        // You can process or display the data as needed
                        // foreach ($csvData as $row) {
                        //     // Example: Display each row as a comma-separated string
                        //     echo implode(', ', $row) . '<br>';
                        // }
        
                        Yii::$app->session->addFlash('success', Html::tag('h4', Yii::t('exchangerate', 'Import Exchange Rates File')) . Yii::t('app', '{number} rows imported.', ['number' => $nR]));
                    } else {
                        Yii::$app->session->addFlash('error', Yii::t('exchangerate', 'Failed to open the CSV file.'));
                    }
                } else {
                    Yii::$app->session->addFlash('error', Yii::t('exchangerate', 'The CSV file does not exist.'));
                }
            }
        }

        return $this->render('import', ['model' => $model]);



    }
    /**
     * Displays a single Exchangerate model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Exchangerate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Exchangerate();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Exchangerate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Exchangerate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Exchangerate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Exchangerate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Exchangerate::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('exchangerate', 'The requested page does not exist.'));
    }
}
