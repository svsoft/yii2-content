<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m190115_155100_add_item_column extends Migration
{
    public function safeUp()
    {
        $itemTable = 'svs_content_item';
        $this->addColumn($itemTable, 'active', $this->boolean()->defaultValue(true));
    }

    public function safeDown()
    {
        $itemTable = 'svs_content_item';
        $this->dropColumn($itemTable, 'active');

        return true;
    }
}
