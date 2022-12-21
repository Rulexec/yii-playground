<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Book[] $books */
/** @var app\models\Author $model */

$encodedAuthorName = Html::encode($model->title);
$this->title = "Author \"{$encodedAuthorName}\"";

?>
<div>
    <div>Author "<?= $encodedAuthorName ?>"</div>

    <?php if (count($books) > 0) { ?>
        <div>Books:</div>
        <?php foreach ($books as $item) { ?>
        <div>
            <a href="<?= Html::encode(Url::toRoute(['book/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        </div>
        <?php } ?>
    <?php } else { ?>
        <div>No books :|</div>
    <?php } ?>
</div>