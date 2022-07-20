<?php

namespace app\controllers;

use app\models\Products;
use app\models\ProductsComments;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Sort;

use yii\imagine\Image;
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
{
    const MAX_COMMENT_LARGE_IMAGE_SIZE = 1000;
    const MAX_COMMENT_PREVIEW_IMAGE_SIZE = 100;

    const COMMENT_ATTACHMENT_TYPE_IMAGE = 1;
    const COMMENT_ATTACHMENT_TYPE_TEXT = 2;

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

        $is_show_submit_form = true;
        $message = null;
        $is_message_success = false;

        if ($this->request->isPost) {
            $this->_saveComment($product, $comment, $is_show_submit_form, $message, $is_message_success);
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
            'commentsDataProvider' => $commentsDataProvider,
            'sort' => $sort,

            'is_show_submit_form' => $is_show_submit_form,
            'message' => $message,
            'is_message_success' => $is_message_success,
        ]);
    }

    /**
     * @param Products $product
     * @param ProductsComments $comment
     */
    protected function _saveComment($product, $comment, &$is_show_submit_form, &$message, &$is_message_success) {

        $is_show_submit_form = true;
        $message = null;
        $is_message_success = false;

        if (!$comment->load($this->request->post())) {
            $message = "Некоторые поля формы содержат ошибки. Проверьте ввод.";
            return;
        }

        $comment->uploaded_file = \yii\web\UploadedFile::getInstance($comment, 'uploaded_file');
        if (!$comment->validate()) {
            $message = "Некоторые поля формы содержат ошибки. Проверьте ввод.";
            return;
        }

        // проверяем картинку на валидность, если она есть
        if (
            $comment->uploaded_file
            && ($comment->uploaded_file->extension != 'txt')
            && !exif_imagetype($comment->uploaded_file->tempName)
        ) {
            $message = "Не смогли загрузить картинку. Проверьте файл с изображением.";
            return;
        }

        if (!$product->addComment($comment)) {
            $message = "Некоторые поля формы содержат ошибки. Проверьте ввод.";
            return;
        }

        $message = "Ваш комментарий добавлен.";
        $is_show_submit_form = false;
        $is_message_success = true;

        if (!$comment->uploaded_file)
            return;

        // раскладываем картинки в поддиректории, чтобы не перегружать файловую систему
        $padded_id = "000000" . $comment->id;
        $level1_folder_name = substr($padded_id, -2);
        $level2_folder_name = substr(substr($padded_id, -4), 0, 2);
        $partial_path = "{$level1_folder_name}/{$level2_folder_name}";
        $comments_attachments_path = \Yii::getAlias('@webroot/comments_attachments');

        if ($comment->uploaded_file->extension == 'txt') {

            $file_name = "{$comment->id}.txt";
            $folder_path = $comments_attachments_path . "/txt/" . $partial_path;
            \yii\helpers\FileHelper::createDirectory($folder_path, $mode = 0775, $recursive = true);
            $comment->uploaded_file->saveAs($folder_path . "/" . $file_name);
            $comment->uploaded_file = null;

            $comment->attachment_type = self::COMMENT_ATTACHMENT_TYPE_TEXT;
            $comment->attachment_path = $partial_path . "/" . $file_name;
            $comment->save();

        } else {

            $image_file_name = "{$comment->id}.jpg";
            $large_image_folder_path = $comments_attachments_path . "/large/" . $partial_path;
            $preview_image_folder_path = $comments_attachments_path . "/preview/" . $partial_path;

            list ($width, $height) = getimagesize($comment->uploaded_file->tempName);
            $src_path = $comment->uploaded_file->tempName;

            $saveUploadedImage = function ($max_size, $dest_folder_path) use ($width, $height, $src_path, $image_file_name) {
                \yii\helpers\FileHelper::createDirectory($dest_folder_path, $mode = 0775, $recursive = true);
                $image = Image::getImagine()->open($src_path);
                if ($width > $max_size || $height > $max_size) {
                    $image = $image->thumbnail(new Box($max_size, $max_size));
                }
                $image->save($dest_folder_path . "/" . $image_file_name , ['quality' => 90]);
            };

            try {

                $saveUploadedImage(self::MAX_COMMENT_LARGE_IMAGE_SIZE, $large_image_folder_path);
                $saveUploadedImage(self::MAX_COMMENT_PREVIEW_IMAGE_SIZE, $preview_image_folder_path);

                $comment->uploaded_file = null;
                $comment->attachment_type = self::COMMENT_ATTACHMENT_TYPE_IMAGE;
                $comment->attachment_path = $partial_path . "/" . $image_file_name;
                $comment->save();

            } catch (\Exception $e) {
                \Yii::$app->errorHandler->logException($e);
                $message = "Комментарий был добавлен, но не смогли сохранить картинку. Возможно, файл поврежден.";
                $is_message_success = false;
            }

        }

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
