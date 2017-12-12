<?php

namespace abcms\shop\migrations;

use yii\db\Migration;

class m171212_203342_add_featured_to_shop_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_product', 'featured', $this->boolean()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('shop_product', 'featured');
    }
}
