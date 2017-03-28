<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добрый день <?= Yii::$app->user->identity->username ?> !</h1>
    </div>
</div>
