<?php

namespace abcms\shop\migrations;

use yii\db\Migration;

class m171206_095854_add_ordering_to_shop_category_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_category', 'ordering', $this->integer()->notNull()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('shop_category', 'ordering');
    }
}
