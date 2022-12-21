<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shop|null $model */

$this->title = 'Create a shop';

?>
<div>
    <form method="POST" action="<?= Html::encode(Url::toRoute(['shop/create'])); ?>">
        <input type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->getCsrfToken()?>">
        <div>
            <div>Title:</div>
            <div><input type="text" name="title" required></div>
        </div>
        <div>
            <button type="submit">Create</button>
        </div>
    </form>
    <?php if ($model) { ?>
        <div><pre><code><?= print_r($model, true); ?></code></pre></div>
    <?php } ?>
</div>