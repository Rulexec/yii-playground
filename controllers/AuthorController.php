<?php

namespace app\controllers;

use app\models\Author;
use Yii;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AuthorController extends Controller
{
    public $layout = 'books_store';

    public function actionList()
    {
        $items = Author::find()->all();

        return $this->render('list', [
            'items' => $items,
        ]);
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $id = intval($request->get('id'));

        $model = $id ? Author::findOne($id) : null;

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('view', [
            'model' => $model,
            'books' => $model->getBooks()->all(),
        ]);
    }

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

            return $this->redirect(['author/edit', 'id' => $model->id], 302);
        }


        return $this->render('create', [
            'model' => null,
        ]);
    }

    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = intval($request->get('id'));
        $model = $id ? Author::findOne($id) : null;

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if ($request->isPost) {
            $success = $model->load($request->post()) && $model->validate();

            if ($success) {
                $model->save(false);
            }

            return $this->redirect(['author/edit', 'id' => $model->id], 302);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    // COPYPASTE 144eead5
    public function actionDelete() {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            throw new MethodNotAllowedHttpException();
        }

        $model = Author::findOne($request->post('id'));

        if ($model) {
            $model->delete();
        }

        $retPath = $request->get('retPath');

        if (!$retPath) {
            // return blank response
            Yii::$app->response->format = Response::FORMAT_RAW;
            return;
        }

        return $this->redirect($retPath, 302);
    }
}