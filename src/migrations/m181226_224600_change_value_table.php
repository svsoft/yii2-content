<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m181226_224600_change_value_table extends Migration
{
    public function safeUp()
    {
        $itemTable = 'svs_content_value';
        $this->alterColumn($itemTable, 'value_float', $this->decimal(10,4));
    }

    public function safeDown()
    {
        $itemTable = 'svs_content_value';
        $this->alterColumn($itemTable, 'value_float', $this->float());

        return true;
    }
}
