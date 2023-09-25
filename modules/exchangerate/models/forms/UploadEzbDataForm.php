<?php

namespace app\modules\exchangerate\models\forms;

use Yii;
use yii\base\Model;

class UploadEzbDataForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $csvFile;

    public $uploadPath;

    public $truncateTable;

    public function rules()
    {
        return [
            [['truncateTable'], 'boolean'],
            [['csvFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'csv'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'csvFile' => Yii::t('exchangerate', 'CSV File'),
            'truncateTable' => Yii::t('exchangerate', 'Truncate Table'),
        ];
    }
    
    public function upload()
    {
        Yii::info($this->csvFile, __METHOD__);
        $this->uploadPath = Yii::getAlias('@app/data/uploads') . '/'
            . uniqid() . '.' . $this->csvFile->extension;
        if ($this->validate()) {
            $this->csvFile->saveAs($this->uploadPath);
            return true;
        } else {
            return false;
        }
    }

}