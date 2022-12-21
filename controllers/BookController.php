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

    public function actionCreate()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $data = $request->post();

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
                    'model' => null,
                    'errors' => $errors,
                ]);
            }

            $isIdsValid = Book::validateAuthorsAndShopsExistance($authorIds, $shopIds);

            if (!$isIdsValid) {
                return $this->render('create', [
                    'model' => null,
                    'errors' => [
                        'authorOrShopNotExists' => true,
                    ],
                ]);
            }

            $model = new Book();
            $model->title = $title;

            $success = $model->validate() && $model->save();
            if (!$success) {
                return $this->render('create', [
                    'model' => null,
                    'errors' => [
                        // title should be valid, so we need check db/fix validation
                        'save500' => true,
                    ],
                ]);
            }

            // create m2m relations
            $success = $model->addAuthorsAndShops($authorIds, $shopIds);

            if (!$success) {
                $model->delete();
                return $this->render('create', [
                    'model' => null,
                    'errors' => [
                        'save500' => true,
                    ],
                ]);
            }

            return $this->render('create', [
                'model' => $model,
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