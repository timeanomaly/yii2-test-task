<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;
use kartik\rating\StarRating;
use yii\widgets\ListView;
use coderius\lightbox2\Lightbox2;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
/* @var $form_model app\models\ProductsComments */
/* @var $message string */
/* @var $is_message_success bool */
/* @var $is_show_submit_form bool */
/* @var $commentsDataProvider yii\data\ActiveDataProvider */
/* @var $sort yii\data\Sort */

$this->title = $model->title;
//$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

coderius\lightbox2\Lightbox2::widget([
    'clientOptions' => [
        'resizeDuration' => 200,
        'wrapAround' => true,
    ]
]);

?>
<div class="products-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="my-section-body">
        <p><i>Это тестовый товар</i></p>
    </div>

    <h2>Комментарии</h2>
    <div class="my-section-body">
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
    </div>

    <?php

    if ($message) {
        ?><div class="alert alert-<?= $is_message_success ? "success" : "danger" ?>"><?= $message ?></div><?php
    }

    if ($is_show_submit_form) {

        ?>
        <h2>Оставить комментарий:</h2>

        <div class="row my-section-body">
            <div class="col-lg-5 ml-4">

                <?php $form = ActiveForm::begin([
                        'id' => 'add-comment-form',
                        'options' => ['enctype' => 'multipart/form-data']
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

                <?= $form->field($form_model, 'uploaded_file')->fileInput()->label('Фото или текстовый файл (опционально)') ?>

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
