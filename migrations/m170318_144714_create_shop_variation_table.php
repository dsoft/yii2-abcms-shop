<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `shop_variation`.
 */
class m170318_144714_create_shop_variation_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('shop_variation_attribute', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
        $this->batchInsert('shop_variation_attribute', ['name'], [   
                ['Size'],
                ['Color'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('shop_variation_attribute');
    }
}
