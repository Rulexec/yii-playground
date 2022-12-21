<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Book[] $books */

$request = Yii::$app->request;

$this->title = 'Books list';

$csrf = <<<CSRF
    <input type="hidden" name="{$request->csrfParam}" value="{$request->getCsrfToken()}">
CSRF;

$currentUrl = Url::current();

?>
<div>
    <?php if (count($books) > 0) { ?>
    <div>Books list:</div>
    <?php foreach ($books as $book) { ?>
    <div>
        <a href="<?= Html::encode(Url::toRoute(['book/view', 'id' => $book->id])); ?>"><?= $book->title ?></a>
        <form
            method="POST"
            action="<?= Html::encode(Url::toRoute(['book/delete', 'retPath' => $currentUrl])); ?>"
            style="display: inline-block"
        >
            <?= $csrf ?>
            <input type="hidden" name="id" value="<?= Html::encode($book->id) ?>">
            <button type="submit">delete</button>
        </form>
    </div>
    <?php } ?>
    <?php } else { ?>
    No books, <?= Html::a('create', ['book/create']); ?>?
    <?php } ?>
</div>