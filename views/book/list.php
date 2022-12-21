<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Book[] $items */

$request = Yii::$app->request;

$this->title = 'Books list';

$csrf = <<<CSRF
    <input type="hidden" name="{$request->csrfParam}" value="{$request->getCsrfToken()}">
CSRF;

$currentUrl = Url::current();

?>
<div>
    <?php if (count($items) > 0) { ?>
    <div>Books list:</div>
    <?php foreach ($items as $item) { ?>
    <div>
        <a href="<?= Html::encode(Url::toRoute(['book/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        <a href="<?= Html::encode(Url::toRoute(['book/edit', 'id' => $item->id])); ?>">edit</a>
        <form
            method="POST"
            action="<?= Html::encode(Url::toRoute(['book/delete', 'retPath' => $currentUrl])); ?>"
            style="display: inline-block"
        >
            <?= $csrf ?>
            <input type="hidden" name="id" value="<?= Html::encode($item->id) ?>">
            <button type="submit">delete</button>
        </form>
    </div>
    <?php } ?>
    <div><?= Html::a('create', ['book/create']); ?></div>
    <?php } else { ?>
    No books, <?= Html::a('create', ['book/create']); ?>?
    <?php } ?>
</div>