<?php
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Book|null $model */
/** @var app\models\Author[]|null $selectedAuthors */
/** @var app\models\Shop[]|null $selectedShops */

$encodedItemTitle = $model ? Html::encode($model->title) : '';

if ($model) {
    $this->title = "Edit \"{$encodedItemTitle}\"";
} else {
    $this->title = 'Create a book';
}

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

if (!isset($selectedAuthors)) $selectedAuthors = null;
if (!isset($selectedShops)) $selectedShops = null;

$selectedAuthorsJson = null;
$selectedShopsJson = null;

if (isset($selectedAuthors) && $selectedAuthors) {
    $selectedAuthorsJson = array_map(
        function ($item) {
            return ['id' => $item->id, 'title' => $item->title];
        },
        $selectedAuthors,
    );
}

if ($selectedShops) {
    $selectedShopsJson = array_map(
        function ($item) {
            return ['id' => $item->id, 'title' => $item->title];
        },
        $selectedShops,
    );
}

?>
<div>
    <!-- inject author/shop titles to not request them -->
    <script>
        window.__autocompleteAuthors = <?= Json::encode($selectedAuthorsJson) ?>;
        window.__autocompleteShops = <?= Json::encode($selectedShopsJson) ?>;
    </script>
    <form
        method="POST"
        action="<?= Html::encode(Url::toRoute($model ? ['book/edit', 'id' => $model->id] : ['book/create'])); ?>"
    >
        <input type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->getCsrfToken()?>">
        <div>
            <div>Title:</div>
            <div><input type="text" name="title" required value="<?= $encodedItemTitle ?>"></div>
        </div>
        <div>
            <div>Authors:</div>
            <div class="selected-authors"></div>
            <div>
                <input
                    type="hidden"
                    name="authors"
                    value="<?= $selectedAuthors ? implode(',', array_map(fn($item) => $item->id, $selectedAuthors)) : '' ?>"
                >
                <input type="text"
                    data-autocomplete-endpoint="<?= Html::encode(Url::toRoute(['author/autocomplete'])); ?>"
                    data-autocomplete-multiselect-input-name="authors"
                    data-autocomplete-multiselect-selected-class="selected-authors"
                    data-autocomplete-obj="__autocompleteAuthors"
                >
            </div>
        </div>
        <div>
            <div>Shops:</div>
            <div class="selected-shops"></div>
            <div>
                <input
                    type="hidden"
                    name="shops"
                    value="<?= $selectedShops ? implode(',', array_map(fn($item) => $item->id, $selectedShops)) : '' ?>"
                >
                <input type="text"
                    data-autocomplete-endpoint="<?= Html::encode(Url::toRoute(['shop/autocomplete'])); ?>"
                    data-autocomplete-multiselect-input-name="shops"
                    data-autocomplete-multiselect-selected-class="selected-shops"
                    data-autocomplete-obj="__autocompleteShops"
                >
            </div>
        </div>
        <div>
            <button type="submit"><?= $model ? 'Save' : 'Create' ?></button>
        </div>
    </form>
    <?php if (isset($errors) && $errors) { ?>
        <div>Errors:</div>
        <div><pre><code><?= print_r($errors, true) ?></code></pre></div>
    <?php } ?>
</div>