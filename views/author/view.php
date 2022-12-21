<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Book[] $books */
/** @var app\models\Author $model */

$request = Yii::$app->request;
$csrf = <<<CSRF
    <input type="hidden" name="{$request->csrfParam}" value="{$request->getCsrfToken()}">
CSRF;

$encodedAuthorName = Html::encode($model->title);
$this->title = "Author \"{$encodedAuthorName}\"";

?>
<div>
    <div>Author "<?= $encodedAuthorName ?>" <?= Html::a('edit', ['author/edit', 'id' => $model->id]); ?></div>

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

    <div>
        <form
            method="POST"
            action="<?= Html::encode(Url::toRoute(['author/delete', 'retPath' => Html::encode(Url::toRoute(['author/list']))])); ?>"
            style="display: inline-block"
        >
            <?= $csrf ?>
            <input type="hidden" name="id" value="<?= Html::encode($model->id) ?>">
            <button type="submit">delete</button>
        </form>
    </div>
</div>