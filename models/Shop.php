<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shop".
 *
 * @property int $id
 * @property string $title
 */
class Shop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop';
    }

    public function formName()
    {
        return '';
    }

    // COPYPASTE: e594b574
    public static function autocompleteByTitle($pattern)
    {
        // ActiveQuery escapes ' and other sql-sensitive chars,
        // but I don't see how to escape % to not allow spam them in LIKE
        $pattern = preg_replace('/%/', '\\%', $pattern);
        $query = Shop::find()->select(['id', 'title'])->where(['LIKE', 'title', $pattern . '%', false])->orderBy('title')->limit(20);
        return $query->asArray()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // 'id' => 'ID',
            'title' => 'Title',
        ];
    }
}