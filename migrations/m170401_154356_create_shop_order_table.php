<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `shop_order`.
 */
class m170401_154356_create_shop_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_order', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'cartId' => $this->integer()->notNull(),
            'total' => $this->decimal(15, 2)->notNull(),
            'note' => $this->string(),
            'firstName' => $this->string()->notNull(),
            'lastName' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'country' => $this->integer()->notNull(),
            'city' => $this->string()->notNull(),
            'address' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'createdTime' => $this->dateTime()->notNull(),
            'updatedTime' => $this->dateTime()->notNull(),
            'ipAddress' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_order');
    }
}
