<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m180316_145947_add_type extends Migration
{
    public function safeUp()
    {
        $typeTable = 'svs_content_type';
        $valueTable = 'svs_content_value';

        $this->addColumn($valueTable, 'value_date', $this->timestamp()->null());
        $this->insert($typeTable, ['label'=>'Дата', 'simple'=>1, 'name'=>'date', 'value_field'=>'value_date']);
        $this->insert($typeTable, ['label'=>'Дата и время', 'simple'=>1, 'name'=>'datetime', 'value_field'=>'value_date']);
    }

    public function safeDown()
    {
        $valueTable = 'svs_content_value';
        $this->dropColumn($valueTable, 'value_date');

        return true;
    }
}
