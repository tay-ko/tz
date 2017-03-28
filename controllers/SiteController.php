<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\components\Locker;

class SiteController extends Controller
{
    /** @var  $locker Locker*/
    private $locker;

    /**
     * @inheritdoc
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
    }

    public function init()
    {
        parent::init();
        $this->locker = Yii::$app->locker;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->locker->resetCounter();
            return $this->redirect('/site/index');
        } elseif($this->locker->getTimer() < 0) {
            $this->locker->pushCounter();
        }

        if($this->locker->isBlocked()) {
            $this->locker->startTimer();
            $this->locker->resetCounter();
            return $this->render('blocked',[
                'message' => 'Вход заблокирован!'
            ]);
        }

        if($this->locker->isStartTimer()) {
            $time = $this->locker->getTimer();
            if ($time > 0) {
                $timer = $this->locker->formatTimer($time);
                return $this->render('blocked', [
                    'message' => "Вход заблокирован на ".$timer['minutes']." минуты и ".$timer['seconds']." секунд !"
                ]);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect('/site/login');
    }
}
