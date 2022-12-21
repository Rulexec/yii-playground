<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shop[] $items */

$request = Yii::$app->request;

$this->title = 'Authors list';

$csrf = <<<CSRF
    <input type="hidden" name="{$request->csrfParam}" value="{$request->getCsrfToken()}">
CSRF;

$currentUrl = Url::current();

?>
<div>
    <?php if (count($items) > 0) { ?>
    <div>Authors list:</div>
    <?php foreach ($items as $item) { ?>
    <div>
        <a href="<?= Html::encode(Url::toRoute(['author/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        <form method="POST" action="<?= Html::encode(Url::toRoute(['author/delete', 'retPath' => $currentUrl])); ?>"
            style="display: inline-block">
            <?= $csrf ?>
            <input type="hidden" name="id" value="<?= Html::encode($item->id) ?>">
            <button type="submit">delete</button>
        </form>
    </div>
    <?php } ?>
    <?php } else { ?>
    No authors, <?= Html::a('create', ['author/create']); ?>?
    <?php } ?>
</div>