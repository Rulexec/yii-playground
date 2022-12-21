<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Book[] $books */

$this->title = 'Books list';
?>
<div>
    <?php if (count($books) > 0) { ?>
    <div>Books list:</div>
    <?php foreach ($books as $book) { ?>
        <div><?= $book->title ?></div>
    <?php } ?>
    <?php } else { ?>
        No books, <?= Html::a('create', ['book/create']); ?>?
    <?php } ?>
</div>
