<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `shop_product`.
 */
class m170317_151324_create_shop_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_product', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'categoryId' => $this->integer()->notNull(),
            'finalPrice' => $this->decimal(15, 2)->notNull(),
            'originalPrice' => $this->decimal(15, 2),
            'description' => $this->text(),
            'availableQuantity' => $this->integer(),
            'brandId' => $this->integer(),
            'albumId' => $this->integer(),
            'active' => $this->boolean()->defaultValue(1),
            'deleted' => $this->boolean()->defaultValue(0),
            'time' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_product');
    }
}
