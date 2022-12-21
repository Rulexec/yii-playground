<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'Create a book';

$this->registerJsFile(
    'https://unpkg.com/autocompleter@7.0.1/autocomplete.min.js',
    ['defer' => '', 'async' => ''],
);
$this->registerCssFile(
    'https://unpkg.com/autocompleter@7.0.1/autocomplete.min.css',
);
$this->registerJsFile(
    '@web/js/autocomplete.js',
    ['type' => 'module'],
);

?>
<div>
    <form method="POST" action="<?= Html::encode(Url::toRoute(['book/create'])); ?>">
        <input type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->getCsrfToken()?>">
        <div>
            <div>Title:</div>
            <div><input type="text" name="title" required></div>
        </div>
        <div>
            <div>Authors:</div>
            <div class="selected-authors"></div>
            <div>
                <input type="hidden" name="authors">
                <input type="text"
                    data-autocomplete-endpoint="<?= Html::encode(Url::toRoute(['author/autocomplete'])); ?>"
                    data-autocomplete-multiselect-input-name="authors"
                    data-autocomplete-multiselect-selected-class="selected-authors">
            </div>
        </div>
        <div>
            <div>Shops:</div>
            <div class="selected-shops"></div>
            <div>
                <input type="hidden" name="shops">
                <input type="text"
                    data-autocomplete-endpoint="<?= Html::encode(Url::toRoute(['shop/autocomplete'])); ?>"
                    data-autocomplete-multiselect-input-name="shops"
                    data-autocomplete-multiselect-selected-class="selected-shops">
            </div>
        </div>
        <div>
            <button type="submit">Create</button>
        </div>
    </form>
    <div><pre><code><?= print_r($model, true) ?></code></pre></div>
</div>