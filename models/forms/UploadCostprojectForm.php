<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\User;
use app\models\Costproject;
use app\models\Expense;

class UploadCostprojectForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $exportFile;

    public $savedPath;


    public function rules()
    {
        return [
            [['exportFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'json', 'checkExtensionByMimeType' => false],
        ];
    }
    
    public function upload()
    {
        $path = Yii::getAlias('@data/uploads/temp');
        $this->savedPath = $path . '/' . $this->exportFile->baseName . '.' . $this->exportFile->extension;
        if ($this->validate()) {
            $this->exportFile->saveAs($this->savedPath);
            return true;
        } else {
            return false;
        }
    }

    public function import()
    {
        // Read JSON file
        $json = file_get_contents($this->savedPath);
        $upload = \yii\helpers\Json::decode($json, true);

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            // Check users
            $foundUsers = [];
            foreach($upload['Users'] as $userData) {
                $user = User::find()->where(['username' => $userData['username']])->one();
                if (!empty($user))
                    $foundUsers[$userData['id']] = $user->id;
            }
            Yii::info($foundUsers, __METHOD__ . ' foundUsers');
            // Create Costproject;
            $costproject = new Costproject();
            $costproject->attributes = $upload['Costproject']['attributes'];
            if(!$costproject->save()) {
                throw new \Exception("Couldn't save new cost project");
            }
            Yii::$app->db->createCommand()->insert('{{%user_costproject}}', [
                'userId' => $foundUsers[$upload['Costproject']['attributes']['created_by']],
                'costprojectId' => $costproject->id,
            ])->execute();
            
            // Add expenses
            foreach($upload['Costproject']['Expenses'] as $expenseData) {
                $expense = new Expense();
                $expense->attributes = $expenseData;
                $expense->costprojectId = $costproject->id;
                if(!$expense->save()) {
                    throw new \Exception("Couldn't save new expense");
                }
            }

            // Add order?
            if(!empty($upload['Costproject']['Order'])) {
                $order = new Order();
                $order->attributes = $upload['Costproject']['Order'];
                $order->userId = $foundUsers[$upload['Costproject']['Order']['userId']];
                if(!$expense->save()) {
                    throw new \Exception("Couldn't save new order info");
                }
            }
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        @unlink($this->savedPath);

        return $costproject->id;
    }

    public function attributeLabels()
    {
        return [
            'exportFile' => Yii::t('app', 'Import File'),
        ];
    }
}