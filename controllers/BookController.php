<?php

namespace app\controllers;

use app\models\Book;
use Yii;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BookController extends Controller
{
    public $layout = 'plain';

    public function actionList()
    {
        $items = Book::find()->all();

        return $this->render('list', [
            'items' => $items,
        ]);
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $id = intval($request->get('id'));

        $model = $id ? Book::findOne($id) : null;

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('view', [
            'model' => $model,
            'shops' => $model->getShops()->all(),
            'authors' => $model->getAuthors()->all(),
        ]);
    }

    private function createOrUpdateModelOrGetErrorResponse(&$model, $data)
    {
        $title = $data['title'];
        $authorIds = BookController::parseMultiselectIds($data['authors']);
        $shopIds = BookController::parseMultiselectIds($data['shops']);

        $titleLength = strlen($title);
        $isTitleValid = 0 < $titleLength && $titleLength <= 255;

        $errors = [];

        if (!$isTitleValid) {
            $errors['title'] = true;
        }
        if ($authorIds === null || !count($authorIds)) {
            $errors['authors'] = true;
        }
        if ($shopIds === null || !count($shopIds)) {
            $errors['shops'] = true;
        }

        if (count($errors)) {
            return $this->render('create', [
                'model' => $model,
                'errors' => $errors,
            ]);
        }

        $isIdsValid = Book::validateAuthorsAndShopsExistance($authorIds, $shopIds);

        if (!$isIdsValid) {
            return $this->render('create', [
                'model' => $model,
                'errors' => [
                    'authorOrShopNotExists' => true,
                ],
            ]);
        }

        $editModel = $model;

        if (!$model) {
            $model = new Book();
        }

        $model->title = $title;

        $success = $model->validate() && $model->save();
        if (!$success) {
            return $this->render('create', [
                'model' => $editModel,
                'errors' => [
                    // title should be valid, so we need check db/fix validation
                    'save500' => true,
                ],
            ]);
        }

        $success = $model->replaceAuthorsAndShops($authorIds, $shopIds);

        if (!$success) {
            if (!$editModel) {
                // we need delete book only on create
                $model->delete();
            }

            return $this->render('create', [
                'model' => $editModel,
                'errors' => [
                    'save500' => true,
                ],
            ]);
        }

        return null;
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $model = null;
            $errorResponse = $this->createOrUpdateModelOrGetErrorResponse($model, $request->post());

            if ($errorResponse) {
                return $errorResponse;
            }

            return $this->redirect(['book/edit', 'id' => $model->id], 302);
        }

        return $this->render('create', [
            'model' => null,
        ]);
    }

    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = intval($request->get('id'));
        $model = $id ? Book::findOne($id) : null;

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if ($request->isPost) {
            $errorResponse = $this->createOrUpdateModelOrGetErrorResponse($model, $request->post());

            if ($errorResponse) {
                return $errorResponse;
            }

            return $this->redirect(['book/edit', 'id' => $model->id], 302);
        }

        $selectedAuthors = $model ? $model->getAuthors()->all() : null;
        $selectedShops = $model ? $model->getShops()->all() : null;

        return $this->render('create', [
            'model' => $model,
            'selectedAuthors' => $selectedAuthors,
            'selectedShops' => $selectedShops,
        ]);
    }

    // COPYPASTE 144eead5
    public function actionDelete() {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            throw new MethodNotAllowedHttpException();
        }

        $model = Book::findOne($request->post('id'));

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

    private static function parseMultiselectIds($str)
    {
        if (!is_string($str)) {
            return null;
        }

        $arr = explode(',', $str);
        $newArr = [];

        foreach ($arr as $idStr) {
            $id = intval($idStr);

            if ($id === 0) {
                return null;
            }

            array_push($newArr, $id);
        }

        return $newArr;
    }
}