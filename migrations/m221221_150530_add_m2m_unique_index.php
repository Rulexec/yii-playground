<?php

use yii\db\Migration;

/**
 * Class m221221_150530_add_m2m_unique_index
 */
class m221221_150530_add_m2m_unique_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->updateM2M([
            'tableName' => 'book_author',
            'leftFieldName' => 'book_id',
            'leftRefTableName' => 'book',
            'rightFieldName' => 'author_id',
            'rightRefTableName' => 'author',
        ]);

        $this->updateM2M([
            'tableName' => 'book_shop',
            'leftFieldName' => 'book_id',
            'leftRefTableName' => 'book',
            'rightFieldName' => 'shop_id',
            'rightRefTableName' => 'shop',
        ]);
    }

    private function updateM2M($options)
    {
        $tableName = $options['tableName'];
        $leftFieldName = $options['leftFieldName'];
        $rightFieldName = $options['rightFieldName'];

        $full = "{$tableName}_{$leftFieldName}_{$rightFieldName}";

        $this->createIndex($full, $tableName, [$leftFieldName, $rightFieldName], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221221_150530_add_m2m_unique_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221221_150530_add_m2m_unique_index cannot be reverted.\n";

        return false;
    }
    */
}
