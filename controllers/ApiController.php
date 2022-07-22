<?php

namespace app\controllers;

use app\models\ProductsComments;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

class ApiController extends ActiveController
{
    public $modelClass = 'app\models\ProductsComments';

    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionGetAllAuthors() {

        $list = \app\models\ProductsComments::find()
            ->select('name')
            ->distinct()
            ->all();

        $author_names = [];
        foreach ($list as $record) {
            $author_names []= $record->name;
        }

        return $author_names;

    }

    public function actionGetCommentsFromIp() {

        $ip = Yii::$app->request->queryParams['ip'];
        if (!$ip)
            return new \yii\web\BadRequestHttpException();

        $comments = \app\models\ProductsComments::find()
            ->where(["ip" => $ip])
            ->all();

        return $comments;

    }

}