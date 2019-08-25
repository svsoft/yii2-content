<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m181211_161900_add_type extends Migration
{
    public function safeUp()
    {
        $valueTable = 'svs_content_value';

        $this->addColumn($valueTable, 'value_datetime', $this->dateTime()->null());
        $this->db->createCommand('update '.$valueTable. ' SET value_datetime = value_date')->execute();
        $this->alterColumn($valueTable, 'value_date', $this->date()->null());
    }

    public function safeDown()
    {
        $valueTable = 'svs_content_value';
        $this->dropColumn($valueTable, 'value_datetime');
        $this->alterColumn($valueTable, 'value_date', $this->timestamp()->null());

        return true;
    }
}
