<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shop_category`.
 */
class m170301_151938_create_shop_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'parentId' => $this->integer(),
            'active' => $this->boolean()->defaultValue(1),
            'deleted' => $this->boolean()->defaultValue(0),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_category');
    }
}
