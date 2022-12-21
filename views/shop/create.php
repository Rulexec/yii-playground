<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shop|null $model */

$encodedItemTitle = $model ? Html::encode($model->title) : '';

if ($model) {
    $this->title = "Edit \"{$encodedItemTitle}\"";
} else {
    $this->title = 'Create a shop';
}

?>
<div>
    <form
        method="POST"
        action="<?= Html::encode(Url::toRoute($model ? ['shop/edit', 'id' => $model->id] : ['shop/create'])); ?>"
    >
        <input type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->getCsrfToken()?>">
        <div>
            <div>Title:</div>
            <div><input type="text" name="title" required value="<?= $encodedItemTitle ?>"></div>
        </div>
        <div>
            <button type="submit"><?= $model ? 'Save' : 'Create' ?></button>
        </div>
    </form>
</div>