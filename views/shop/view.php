<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shop $model */
/** @var app\models\Book[] $books */
/** @var app\models\Author[] $authors */

$request = Yii::$app->request;
$csrf = <<<CSRF
    <input type="hidden" name="{$request->csrfParam}" value="{$request->getCsrfToken()}">
CSRF;

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

    <div>
        <form
            method="POST"
            action="<?= Html::encode(Url::toRoute(['shop/delete', 'retPath' => Html::encode(Url::toRoute(['shop/list']))])); ?>"
            style="display: inline-block"
        >
            <?= $csrf ?>
            <input type="hidden" name="id" value="<?= Html::encode($model->id) ?>">
            <button type="submit">delete</button>
        </form>
    </div>
</div>