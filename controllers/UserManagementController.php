<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

use app\components\BaseController;

use app\models\User;
use app\models\UserSearch;

use app\models\enums\StatusRyg;

/**
 * UserController implements aditional management actions for User model.
 */
class UserManagementController extends BaseController
{
    // {{{ Members
    public $fluid = true;

    public $sessionUserLaunchMarketIdKey = 'User_LaunchMarketId';
    // }}} 
    // {{{ behaviors;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // Only apply ACF to these actions:
                'only' => ['roles-and-users'],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['roles-and-users'],
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
            // For Clear Filters:
            'clearFilterState' => \thrieu\grid\ClearFilterStateBehavior::className(),
        ];
    } // }}} 
    // {{{ actionRolesAndUsers
    /**
     * Lists all Roles and User models.
     * @return mixed
     */
    public function actionRolesAndUsers()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('roles-and-users', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    } // }}}
}
