<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `shop_product_variation_attribute`.
 */
class m170318_145809_create_shop_product_variation_attribute_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_product_variation_attribute', [
            'id' => $this->primaryKey(),
            'variationId' => $this->integer()->notNull(),
            'attributeId' => $this->integer()->notNull(),
            'value' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_product_variation_attribute');
    }
}
