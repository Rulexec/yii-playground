<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shop $model */
/** @var app\models\Book[] $books */
/** @var app\models\Author[] $authors */

$encodedShopName = Html::encode($model->title);
$this->title = "Shop \"{$encodedShopName}\"";

?>
<div>
    <div>Shop "<?= $encodedShopName ?>"</div>

    <?php if (count($books) > 0) { ?>
        <div>Books:</div>
        <?php foreach ($books as $item) { ?>
        <div>
            <a href="<?= Html::encode(Url::toRoute(['book/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        </div>
        <?php } ?>

        <div>Authors:</div>
        <?php foreach ($authors as $item) { ?>
        <div>
            <a href="<?= Html::encode(Url::toRoute(['author/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        </div>
        <?php } ?>
    <?php } else { ?>
        <div>No books in this shop</div>
    <?php } ?>
</div>