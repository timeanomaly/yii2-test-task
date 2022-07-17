<?php

/** @var yii\web\View $this */
/* @var $products app\models\Products[] */

use yii\helpers\Url;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <h1>Выберите товар для просмотра комментариев:</h1>
    <ul>
        <?php
        foreach ($products as $product) {
            ?><li><a href="<?= Url::toRoute(["products/view", 'id' => $product->id]) ?>"><?= $product->title ?></a></li><?php
        }
        ?>
    </ul>
</div>
