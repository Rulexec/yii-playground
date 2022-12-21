<?php

namespace app\models;

use SQLite3;
use Yii;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
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

    public function relations()
    {
        return array(
            'authors'=>array(self::HAS_MANY, 'Author', 'author_id'),
            'shops'=>array(self::HAS_MANY, 'Book', 'owner_id'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])->viaTable('book_author', ['book_id' => 'id']);
    }

    public function getShops()
    {
        return $this->hasMany(Shop::class, ['id' => 'shop_id'])->viaTable('book_shop', ['book_id' => 'id']);
    }

    public function replaceAuthorsAndShops($authorIds, $shopIds): bool
    {
        $db = Yii::$app->db;

        $authorPlaceholders = implode(
            ',',
            array_map(
                function ($index) {
                    return "(:bookId,:author{$index})";
                },
                array_keys($authorIds),
            ),
        );
        $authorQuery = <<<SQL
            INSERT INTO book_author(book_id,author_id)
                VALUES {$authorPlaceholders}
                ON CONFLICT(book_id,author_id) DO NOTHING;
        SQL;
        $authorValues = array_reduce($authorIds, function ($acc, $id) {
            $acc[0]["author{$acc[1]}"] = $id;
            $acc[1] += 1;
            return $acc;
        }, [[], 0])[0];

        $shopPlaceholders = implode(
            ',',
            array_map(
                function ($index) {
                    return "(:bookId,:shop{$index})";
                },
                array_keys($shopIds),
            ),
        );
        $shopQuery = <<<SQL
            INSERT INTO book_shop(book_id,shop_id)
                VALUES {$shopPlaceholders}
                ON CONFLICT(book_id,shop_id) DO NOTHING;
        SQL;
        $shopValues = array_reduce($shopIds, function ($acc, $id) {
            $acc[0]["shop{$acc[1]}"] = $id;
            $acc[1] += 1;
            return $acc;
        }, [[], 0])[0];

        $query = <<<SQL
            DELETE FROM book_author WHERE book_id = :bookId;
            DELETE FROM book_shop WHERE book_id = :bookId;
            {$authorQuery}
            {$shopQuery}
        SQL;

        $command = $db->createCommand($query);

        $command->bindValue('bookId', $this->id);
        $command->bindValues($authorValues);
        $command->bindValues($shopValues);

        $command->execute();

        // TODO: wrap sql into BEGIN/ROLLBACK?
        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $db = Yii::$app->db;

        $query = <<<SQL
            DELETE FROM book_author WHERE book_id = :bookId;
            DELETE FROM book_shop WHERE book_id = :bookId;
        SQL;

        $command = $db->createCommand($query);

        $command->bindValue('bookId', $this->id);

        $command->execute();
    }

    /**
     * Accepts lists of ids of authors and shops and check that they are exists,
     * this is needed because SQLite does not supports foreign keys
     * @return mixed
     */
    public static function validateAuthorsAndShopsExistance($authorIds, $shopIds)
    {
        $db = Yii::$app->db;

        // Generate list of placeholders to escape them
        $authorPlaceholders = implode(',', array_map(fn($index) => ":author{$index}", array_keys($authorIds)));
        $authorValues = array_reduce($authorIds, function ($acc, $id) {
            $acc[0]["author{$acc[1]}"] = $id;
            $acc[1] += 1;
            return $acc;
        }, [[], 0])[0];
        $shopPlaceholders = implode(',', array_map(fn($index) => ":shop{$index}", array_keys($shopIds)));
        $shopValues = array_reduce($shopIds, function ($acc, $id) {
            $acc[0]["shop{$acc[1]}"] = $id;
            $acc[1] += 1;
            return $acc;
        }, [[], 0])[0];

        // Count number of existing ids to compare them with list from arguments
        $query = <<<SQL
            SELECT ((SELECT count(*) FROM author WHERE id IN ($authorPlaceholders)) + 
                    (SELECT count(*) FROM shop WHERE id IN ($shopPlaceholders))) as count
        SQL;

        $command = $db->createCommand($query);

        $command->bindValues($authorValues);
        $command->bindValues($shopValues);

        $count = $command->queryOne()['count'];

        return intval($count) === (count($authorIds) + count($shopIds));
    }
}
