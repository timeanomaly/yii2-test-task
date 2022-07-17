<?php
// _list_item.php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\rating\StarRating;

/* @var $model app\models\ProductsComments */

?>

<article class="item products-comments-list-comment" data-key="<?= $model->id; ?>">
    <h2 class="title products-comments-list-title">
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
    </h2>
    <p><?= HTML::encode($model->content) ?></p>
    <?php
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
</article>