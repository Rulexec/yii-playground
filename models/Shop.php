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

    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])->viaTable('book_shop', ['shop_id' => 'id']);
    }

    public function getAuthors()
    {
        $db = Yii::$app->db;

        $query = <<<SQL
            SELECT author_id
            FROM book_author
            INNER JOIN book_shop
            ON book_shop.shop_id = :shopId AND book_author.book_id = book_shop.book_id
        SQL;

        $command = $db->createCommand($query);
        $command->bindValue('shopId', $this->id);

        // select all shops where this book was present and all authors of this book
        $result = $command->queryAll();
        $ids = array_map(fn($row) => $row['author_id'], $result);

        return Author::find()->where(['in', 'id', $ids]);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $db = Yii::$app->db;

        $query = <<<SQL
            DELETE FROM book_shop WHERE shop_id = :shopId
        SQL;

        $command = $db->createCommand($query);

        $command->bindValue('shopId', $this->id);

        $command->execute();
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