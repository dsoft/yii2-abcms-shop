<?php
namespace abcms\shop\migrations;

use yii\db\Migration;

class m170322_131034_add_shop_product_indexes extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `shop_product` ADD FULLTEXT INDEX fulltext_name (`name`)");
        $this->execute("ALTER TABLE `shop_product` ADD FULLTEXT INDEX fulltext_description (`description`)");
        $this->execute("ALTER TABLE `shop_product` ADD FULLTEXT INDEX fulltext_name_description (`name`, `description`)");
    }

    public function down()
    {
        $this->dropIndex('fulltext_name', 'shop_product');
        $this->dropIndex('fulltext_description', 'shop_product');
        $this->dropIndex('fulltext_name_description', 'shop_product');
    }

}
