<?php

namespace app\controllers;

use app\models\Shop;
use Yii;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ShopController extends Controller
{
    public $layout = 'plain';

    public function actionList()
    {
        $items = Shop::find()->all();

        return $this->render('list', [
            'items' => $items,
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

        $autocomplete = Shop::autocompleteByTitle($pattern);

        return $this->asJson([
            'items' => $autocomplete,
        ]);
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $id = intval($request->get('id'));

        $model = $id ? Shop::findOne($id) : null;

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('view', [
            'model' => $model,
            'books' => $model->getBooks()->all(),
            'authors' => $model->getAuthors()->all(),
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $model = new Shop();
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

    // COPYPASTE 144eead5
    public function actionDelete() {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            throw new MethodNotAllowedHttpException();
        }

        $model = Shop::findOne($request->post('id'));

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