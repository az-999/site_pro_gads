<?php

namespace app\controllers;

use app\models\Keyword;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class KeywordController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $query = Keyword::find()->orderBy(['id' => SORT_DESC]);

        $source = Yii::$app->request->get('source');
        $status = Yii::$app->request->get('status');
        $language = Yii::$app->request->get('language');
        $rejectReason = Yii::$app->request->get('reject_reason');

        if ($source) {
            $query->andWhere(['source' => $source]);
        }
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        if ($language) {
            $query->andWhere(['language' => $language]);
        }
        if ($rejectReason) {
            $query->andWhere(['reject_reason' => $rejectReason]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'sourceLabels' => Keyword::sourceLabels(),
            'statusLabels' => Keyword::statusLabels(),
            'filters' => compact('source', 'status', 'language', 'rejectReason'),
        ]);
    }
}
