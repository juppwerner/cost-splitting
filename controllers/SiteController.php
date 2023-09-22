<?php

namespace app\controllers;

use Yii;
use yii\bootstrap4\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

use app\components\BaseController as Controller;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    // {{{ behaviors
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    } // }}} 
    // {{{ actions
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [ // {{{ 
                'class' => 'yii\web\ErrorAction',
            ], // }}} 
            'captcha' => [ // {{{ 
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ], // }}} 
            'page' => [ // {{{ 
                'class' => 'yii\web\ViewAction',
            ], // }}} 
        ];
    } // }}} 

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userCostprojects = \app\models\Costproject::find()
            ->select(['costproject.*'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id]);
        $costprojects = $userCostprojects->count();

        // User's cost projects
        $userCostprojects = $userCostprojects->column();
        if($userCostprojects===array())
            $userCostprojects = [0];

        $query = \app\models\Expense::find()->with('costproject');
        $query->andFilterWhere([
            'costprojectId' => $userCostprojects
        ]);
        $expenses = $query->count();

        return $this->render('index', ['costprojects'=>$costprojects, 'expenses' => $expenses]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['contactForm.recipientEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
