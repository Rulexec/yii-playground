<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shop[] $items */

$request = Yii::$app->request;

$this->title = 'Shops list';

$csrf = <<<CSRF
    <input type="hidden" name="{$request->csrfParam}" value="{$request->getCsrfToken()}">
CSRF;

$currentUrl = Url::current();

?>
<div>
    <?php if (count($items) > 0) { ?>
    <div>Shops list:</div>
    <?php foreach ($items as $item) { ?>
    <div>
        <a href="<?= Html::encode(Url::toRoute(['shop/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        <a href="<?= Html::encode(Url::toRoute(['shop/edit', 'id' => $item->id])); ?>">edit</a>
        <form method="POST" action="<?= Html::encode(Url::toRoute(['shop/delete', 'retPath' => $currentUrl])); ?>"
            style="display: inline-block">
            <?= $csrf ?>
            <input type="hidden" name="id" value="<?= Html::encode($item->id) ?>">
            <button type="submit">delete</button>
        </form>
    </div>
    <?php } ?>
    <div><?= Html::a('create', ['shop/create']); ?></div>
    <?php } else { ?>
    No shops, <?= Html::a('create', ['shop/create']); ?>?
    <?php } ?>
</div>