<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `shop_cart_product`.
 */
class m170329_121226_create_shop_cart_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_cart_product', [
            'id' => $this->primaryKey(),
            'cartId' => $this->integer()->notNull(),
            'productId' => $this->integer()->notNull(),
            'variationId' => $this->integer(),
            'price' => $this->decimal(15, 2)->null(),
            'quantity' => $this->integer()->defaultValue(1),
            'time' => $this->dateTime(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_cart_product');
    }
}
