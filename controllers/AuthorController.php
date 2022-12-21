<?php

namespace app\controllers;

use app\models\Author;
use Yii;
use yii\web\Controller;

class AuthorController extends Controller
{
    public $layout = 'plain';

    // COPYPASTE: 72952b73
    public function actionAutocomplete()
    {
        $pattern = Yii::$app->request->get('pattern');
        if (!is_string($pattern)) {
            Yii::$app->response->statusCode = 400;
            return $this->asJson([
                'error' => 'no pattern',
            ]);
        }

        $autocomplete = Author::autocompleteByTitle($pattern);

        return $this->asJson([
            'items' => $autocomplete,
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $model = new Author();
            $success = $model->load($request->post()) && $model->validate();

            if ($success) {
                $model->save(false);
            }

            return $this->render('create', [
                'model' => [$request->post(), $success ? 'true' : 'false', $model],
            ]);
        }


        return $this->render('create', [
            'model' => null,
        ]);
    }
}