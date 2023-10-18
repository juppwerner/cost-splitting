<?php
namespace app\controllers;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

use app\components\BaseController as Controller;

class MaintenanceController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}