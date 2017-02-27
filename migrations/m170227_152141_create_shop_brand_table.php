<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shop_brand`.
 */
class m170227_152141_create_shop_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_brand', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'active' => $this->boolean()->defaultValue(1),
            'deleted' => $this->boolean()->defaultValue(0),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_brand');
    }
}
