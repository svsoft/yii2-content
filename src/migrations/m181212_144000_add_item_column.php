<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m181212_144000_add_item_column extends Migration
{
    public function safeUp()
    {
        $itemTable = 'svs_content_item';
        $this->addColumn($itemTable, 'sort', $this->integer()->defaultValue(1000));
    }

    public function safeDown()
    {
        $itemTable = 'svs_content_item';
        $this->dropColumn($itemTable, 'sort');

        return true;
    }
}
