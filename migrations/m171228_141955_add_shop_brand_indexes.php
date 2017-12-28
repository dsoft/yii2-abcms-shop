<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

class m171228_141955_add_shop_brand_indexes extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `shop_brand` ADD FULLTEXT INDEX fulltext_name (`name`)");
    }

    public function down()
    {
        $this->dropIndex('fulltext_name', 'shop_brand');
    }
}
