<?php

use yii\db\Migration;
use yii\db\sqlite\Schema;

/**
 * Class m221212_162440_init
 */
class m221212_162440_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
        ]);

        $this->createTable('author', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
        ]);

        $this->createIndex('author_title', 'author', 'title');

        $this->createTable('shop', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
        ]);

        $this->createIndex('shop_title', 'shop', 'title');

        $this->createM2M([
            'tableName' => 'book_author',
            'leftFieldName' => 'book_id',
            'leftRefTableName' => 'book',
            'rightFieldName' => 'author_id',
            'rightRefTableName' => 'author',
        ]);

        $this->createM2M([
            'tableName' => 'book_shop',
            'leftFieldName' => 'book_id',
            'leftRefTableName' => 'book',
            'rightFieldName' => 'shop_id',
            'rightRefTableName' => 'shop',
        ]);
    }

    private function createM2M($options)
    {
        $tableName = $options['tableName'];
        $leftFieldName = $options['leftFieldName'];
        $rightFieldName = $options['rightFieldName'];

        $this->createTable($options['tableName'], [
            $leftFieldName => $this->integer()->notNull(),
            $rightFieldName => $this->integer()->notNull(),
        ]);

        $leftFull = "{$tableName}_{$leftFieldName}";
        $rightFull = "{$tableName}_{$rightFieldName}";
        $full = "{$tableName}_{$leftFieldName}_{$rightFieldName}";

        $this->createIndex($leftFull, $tableName, $leftFieldName);
        $this->createIndex($rightFull, $tableName, $rightFieldName);
        $this->createIndex($full, $tableName, [$leftFieldName, $rightFieldName], true);

        // Not supported by SQLite
        // $this->addForeignKey($leftFull, $tableName, $leftFieldName, $options['leftRefTableName'], 'id', 'CASCADE');
        // $this->addForeignKey($rightFull, $tableName, $rightFieldName, $options['rightRefTableName'], 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221212_162440_init cannot be reverted.\n";

        return false;
    }

/*
// Use up()/down() to run migration code without a transaction.
public function up()
{
}
public function down()
{
echo "m221212_162440_init cannot be reverted.\n";
return false;
}
*/
}