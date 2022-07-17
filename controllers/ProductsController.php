<?php

namespace app\controllers;

use app\models\Products;
use app\models\ProductsComments;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Sort;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Displays a single Products model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $comment = new ProductsComments();
        $product = $this->findModel($id);

        $is_comment_submitted = false;
        $is_comment_submit_success = false;
        if ($this->request->isPost) {
            $is_comment_submitted = true;
            if ($comment->load($this->request->post()) && $product->addComment($comment)) {
                $is_comment_submit_success = true;
            }
        }

        $sort = new Sort([
            'attributes' => [
                'date' => [
                    'asc' => ['date' => SORT_ASC],
                    'desc' => ['date' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => "дате добавления",
                ],
                'rating' => [
                    'asc' => ['rating' => SORT_ASC],
                    'desc' => ['rating' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => "рейтингу",
                ],
            ],
        ]);

        $commentsDataProvider = new \yii\data\ActiveDataProvider([
            'query' => ProductsComments::find()
                ->where(['product_id' => $id])
                ->orderBy($sort->orders),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $this->render('view', [
            'model' => $product,
            'form_model' => $comment,
            'is_comment_submitted' => $is_comment_submitted,
            'is_comment_submit_success' => $is_comment_submit_success,
            'commentsDataProvider' => $commentsDataProvider,
            'sort' => $sort,
        ]);
    }

    /**
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Products::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
