<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

use app\models\Costproject;
use app\models\Expense;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Joachim Werner <joachim.werner@diggin-data.de>
 */
class LoadSampleDataController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @bool $purged Wheter to purge exoisting data or not
     * @return int Exit code
     */
    public function actionIndex($purge=false)
    {
        // Purge existing records?
        if($purge) {
            echo 'Purging data...'.PHP_EOL;
            \app\models\Costproject::deleteAll();
            \app\models\Expense::deleteAll();
            \app\models\Costitem::deleteAll();
            \Yii::$app->db->createCommand('ALTER TABLE `costproject` AUTO_INCREMENT=1;')->execute();
            \Yii::$app->db->createCommand('ALTER TABLE `expense` AUTO_INCREMENT=1;')->execute();
            \Yii::$app->db->createCommand('ALTER TABLE `costitem` AUTO_INCREMENT=1;')->execute();
            \Yii::$app->db->createCommand('SET foreign_key_checks = 0;')->execute();
        }

        $csvProjects = <<< EOL
id;title;participants
1;HartwigstraßenWG;"Anna__CR__Ben__CR__Clara"
EOL;
        $csvExpenses = <<< EOL
title;costprojectId;payedBy;itemDate;amount;splitting
Internet XYZ 7/2022;1;Anna;2022-08-01;39.95;EQUAL
Miete 8/2022;1;Ben;2022-08-03;"365.0";EQUAL
Abschlag Stadtwerke 8/22;1;Clara;2022-08-04;"96.0";EQUAL
Putzmittel;1;Anna;2022-08-11;13.79;EQUAL
Obst und Gemüse (Markt);1;Clara;2022-08-13;"14.5";EQUAL
Getränke Party;1;Ben;2022-08-13;"77.60";EQUAL
Lebensmittel Rewe;1;Ben;2022-08-30;28.56;EQUAL
EOL;
        $datasets = [
            ['name'=>'projects', 'label'=>'Cost Project', 'model'=>'Costproject'],
            ['name'=>'expenses', 'label'=>'Expense', 'model'=>'Expense'],
        ];
        foreach($datasets as $dataset) {
            $csvSet = 'csv'.ucfirst($dataset['name']);
            $lines = explode("\n", str_replace("\r\n", "\n", $$csvSet));
            $numCreated = 0;
            $numErrors = 0;
            foreach($lines as $n=>$line) {
                $values = explode(';', $line);
                if($n==0) {
                    $attributes = $values;
                    continue;
                }
                $model = \Yii::createObject("\\app\\models\\".$dataset['model']);
                foreach($attributes as $m=>$attribute) {
                    $model->$attribute = str_replace('__CR__', "\n", $values[$m]);
                    if(substr($model->$attribute, 0, 1)==='"')
                        $model->$attribute = substr($model->$attribute, 1);
                    if(substr($model->$attribute, -1)==='"')
                        $model->$attribute = substr($model->$attribute, 0, -1);

                    if($attribute==='amount')
                        $model->$attribute = (float)$model->$attribute;
                }
                if(!$model->save()) {
                    $numErrors++;
                    echo 'Error!'.PHP_EOL;
                    echo 'Line: '.$line.PHP_EOL;
                    echo \yii\helpers\VarDumper::dumpAsString($model->errors).PHP_EOL;
                } else {
                    $numCreated++;
                    echo $dataset['label'] . ' created'.PHP_EOL;
                }
            }
            echo $dataset['label'] . ' records created: '.$numCreated.', errors: '.$numErrors.PHP_EOL;
        }
        return ExitCode::OK;
    }
}
