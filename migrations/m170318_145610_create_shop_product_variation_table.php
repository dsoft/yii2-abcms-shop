<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `shop_product_variation`.
 */
class m170318_145610_create_shop_product_variation_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_product_variation', [
            'id' => $this->primaryKey(),
            'productId' => $this->integer()->notNull(),
            'quantity' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_product_variation');
    }
}
