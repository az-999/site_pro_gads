<?php

namespace app\controllers;

use app\models\Keyword;
use app\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'logout'],
                'rules' => [
                    [
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex(): string
    {
        $counts = [
            'total' => Keyword::find()->count(),
            'clean' => Keyword::find()->where(['status' => Keyword::STATUS_CLEAN])->count(),
            'rejected' => Keyword::find()->where(['status' => Keyword::STATUS_REJECTED])->count(),
            'ready' => Keyword::find()->where(['status' => Keyword::STATUS_READY])->count(),
            'used' => Keyword::find()->where(['status' => Keyword::STATUS_USED])->count(),
            'forbidden' => Keyword::find()->where(['status' => Keyword::STATUS_FORBIDDEN])->count(),
            'raw' => Keyword::find()->where(['status' => Keyword::STATUS_RAW])->count(),
        ];

        return $this->render('index', ['counts' => $counts]);
    }

    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }

        $this->layout = 'login';
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['site/index']);
        }

        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->redirect(['site/login']);
    }
}
