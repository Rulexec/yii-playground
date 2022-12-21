<?php

namespace app\controllers;

use app\models\Author;
use app\models\Book;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class BookController extends Controller
{
    public $layout = 'plain';

    public function actionList()
    {
        // $db = Yii::$app->db;
        $books = Book::find()->all();

        return $this->render('list', [
            'books' => $books,
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