<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m180324_110547_add_type extends Migration
{
    public function safeUp()
    {
        $typeTable = 'svs_content_type';
        $valueTable = 'svs_content_value';

        $this->addColumn($valueTable, 'value_boolean', $this->boolean()->null());
        $this->insert($typeTable, ['label'=>'Флаг', 'simple'=>1, 'name'=>'boolean', 'value_field'=>'value_boolean']);
    }

    public function safeDown()
    {
        $valueTable = 'svs_content_value';
        $this->dropColumn($valueTable, 'value_boolean');

        return true;
    }
}
