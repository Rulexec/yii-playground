<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shop[] $shops */
/** @var app\models\Book $model */
/** @var app\models\Author[] $authors */

$request = Yii::$app->request;
$csrf = <<<CSRF
    <input type="hidden" name="{$request->csrfParam}" value="{$request->getCsrfToken()}">
CSRF;

$encodedBookName = Html::encode($model->title);
$this->title = "Book \"{$encodedBookName}\"";

?>
<div>
    <div>Book "<?= $encodedBookName ?>" <?= Html::a('edit', ['book/edit', 'id' => $model->id]); ?></div>

    <?php if (count($authors) > 0) { ?>
        <div>Authors:</div>
        <?php foreach ($authors as $item) { ?>
        <div>
            <a href="<?= Html::encode(Url::toRoute(['author/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        </div>
        <?php } ?>
    <?php } else { ?>
        <div>No authors :|</div>
    <?php } ?>

    <?php if (count($shops) > 0) { ?>
        <div>Shops:</div>
        <?php foreach ($shops as $item) { ?>
        <div>
            <a href="<?= Html::encode(Url::toRoute(['shop/view', 'id' => $item->id])); ?>"><?= $item->title ?></a>
        </div>
        <?php } ?>
    <?php } else { ?>
        <div>No shops selling this book :(</div>
    <?php } ?>

    <div>
        <form
            method="POST"
            action="<?= Html::encode(Url::toRoute(['book/delete', 'retPath' => Html::encode(Url::toRoute(['book/list']))])); ?>"
            style="display: inline-block"
        >
            <?= $csrf ?>
            <input type="hidden" name="id" value="<?= Html::encode($model->id) ?>">
            <button type="submit">delete</button>
        </form>
    </div>
</div>