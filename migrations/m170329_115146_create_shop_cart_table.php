<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `shop_cart`.
 */
class m170329_115146_create_shop_cart_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_cart', [
            'id' => $this->primaryKey(),
            'hash' => $this->string()->unique(),
            'userId' => $this->integer(),
            'createdTime' => $this->dateTime()->notNull(),
            'updatedTime' => $this->dateTime()->notNull(),
            'closed' => $this->boolean()->defaultValue(0),
            'typeId' => $this->smallInteger()->defaultValue(1),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_cart');
    }
}
