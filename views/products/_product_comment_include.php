<?php
// _list_item.php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\rating\StarRating;
use app\controllers\ProductsController;

/* @var $model app\models\ProductsComments */

?>

<article class="item products-comments-list-comment clearfix" data-key="<?= $model->id; ?>">
    <h3 class="title products-comments-list-title">
        <span class="products-comments-list-author_name"><?= $model->name ?></span>
        <?= StarRating::widget([
            //'model' => $model,
            //'attribute' => "rating",
            'name' => "comment_rating_{$model->id}",
            "value" => $model->rating,
            'id' => $model->id,
            'pluginOptions' => [
                'showClear' => false,
                'readonly' => true,
                'showCaption' => false,
                'size' => 'sm',
                'displayOnly' => true
            ]
        ]) ?>
    </h3>
    <div class="products-comments-list-comment-body">
        <p>
            <?php
            if ($model->attachment_type == ProductsController::COMMENT_ATTACHMENT_TYPE_IMAGE) {
                ?><a href="<?= Yii::getAlias("@web/comments_attachments/large/" . $model->attachment_path); ?>" data-lightbox="roadtrip">
                    <?= Html::img('@web/comments_attachments/preview/' . $model->attachment_path, ['class' => 'products-comments-list-image']); ?>
                </a><?php
            } elseif ($model->attachment_type == ProductsController::COMMENT_ATTACHMENT_TYPE_TEXT) {
                ?><a href="<?= Yii::getAlias("@web/comments_attachments/txt/" . $model->attachment_path); ?>" target="_blank">
                    <?= Html::img('@web/design/text-attachment-icon.png', ['class' => 'products-comments-list-image']); ?>
                </a><?php
            }
            ?>
            <?= HTML::encode($model->content) ?>
        </p>
        <?php
        /*if ($model->attachment_path) {
            ?><div class="clearfix">&nbsp;</div><?php
        }*/

        if ($model->advantages) {
            ?>
                <p><b>Преимущества:</b></p>
                <p class="pl-3"><?= HTML::encode($model->advantages) ?></p>
            <?php
        }
        if ($model->disadvantages) {
            ?>
                <p><b>Недостатки:</b></p>
                <p class="pl-3"><?= HTML::encode($model->disadvantages) ?></p>
            <?php
        }
        ?>
    </div>
</article>