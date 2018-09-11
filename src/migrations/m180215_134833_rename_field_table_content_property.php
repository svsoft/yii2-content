<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m180215_134833_rename_field_table_content_property extends Migration
{
    public function safeUp()
    {
        $property = 'svs_content_property';
        $type = 'svs_content_type';
        $this->dropForeignKey($property . '_property_type_id', $property);
        $this->renameColumn($property,'property_type_id','type_id');
        $this->addForeignKey($property . '_type_id', $property, 'type_id', $type, 'type_id', 'RESTRICT');
    }

    public function safeDown()
    {
        $property = 'svs_content_property';
        $type = 'svs_content_type';
        $this->dropForeignKey($property . '_type_id', $property);
        $this->renameColumn($property,'type_id','property_type_id');
        $this->addForeignKey($property . '_property_type_id', $property, 'property_type_id', $type, 'type_id', 'RESTRICT');
        return true;
    }
}
