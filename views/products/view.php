<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;
use kartik\rating\StarRating;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
/* @var $form_model app\models\ProductsComments */
/* @var $is_comment_submitted bool */
/* @var $is_comment_submit_success bool */
/* @var $commentsDataProvider yii\data\ActiveDataProvider */
/* @var $sort yii\data\Sort */

$this->title = $model->title;
//$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="products-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><i>Это тестовый товар</i></p>

    <h2>Комментарии</h2>
    <p>Сортировать по <?= $sort->link('date') . ' | ' . $sort->link('rating') ?></p>
    <?= ListView::widget([
        'dataProvider' => $commentsDataProvider,
        'options' => [
            'tag' => 'div',
            'class' => 'list-wrapper products-comments-list',
            'id' => 'list-wrapper',
        ],
        "summary" => 'Показаны <b>{begin, number}-{end, number}</b> из <b>{totalCount, number}</b> комментариев.',
        'pager' => [
            'class' => \yii\bootstrap4\LinkPager::class,
            'firstPageLabel' => 'Первая',
            'lastPageLabel' => 'Последняя',
            'nextPageLabel' => '&gt;',
            'prevPageLabel' => '&lt;',
            'maxButtonCount' => 5,
        ],
        'itemView' => '_product_comment_include',
    ]); ?>

    <?php

    if ($is_comment_submitted && $is_comment_submit_success) {

        ?><div class="alert alert-success">Ваш комментарий добавлен</div><?php

    } else {

        if ($is_comment_submitted && !$is_comment_submit_success) {
            ?><div class="alert alert-danger">Не смогли добавить комментарий! Проверьте поля!</div><?php
        }

        ?>
        <h2>Оставить комментарий:</h2>

        <div class="row">
            <div class="col-lg-5 ml-4">

                <?php $form = ActiveForm::begin([
                        'id' => 'add-comment-form',
                        //'options' => ['enctype' => 'multipart/form-data']
                ]); ?>

                <?= $form->field($form_model, 'name')->textInput()->hint('Латинские буквы и цифры')->label('Ваш ник') ?>

                <?= $form->field($form_model, 'email')->hint('test@test.com')->label('Ваша почта') ?>

                <?= $form->field($form_model, 'rating')->label('Рейтинг')->widget(StarRating::classname(), [
                    'pluginOptions' => [
                        'size'=>'lg',
                        'stars' => 5,
                        'min' => 0,
                        'max' => 5,
                        'step' => 1,
                        'showClear' => false,
                        'starCaptions' => [
                            1 => 'Совсем плохо',
                            2 => 'Не очень',
                            3 => 'Средне',
                            4 => 'Хорошо',
                            5 => 'Отлично',
                        ],
                        'defaultCaption' => '{rating} звёзд',
                        'clearCaption' => 'Рейтинг не установлен'
                    ]
                ]); ?>

                <!--?= $form->field($form_model, 'uploaded_image')->fileInput()->label('Фото (опционально)') ?-->

                <?= $form->field($form_model, 'content')->textarea(['rows' => 6])->label('Напишите отзыв') ?>

                <?= $form->field($form_model, 'advantages')->textarea(['rows' => 6])->label('Преимущества (опционально)') ?>

                <?= $form->field($form_model, 'disadvantages')->textarea(['rows' => 6])->label('Недостатки (опционально)') ?>

                <div class="form-group">
                    <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
        <?php

    }

    ?>

</div>
