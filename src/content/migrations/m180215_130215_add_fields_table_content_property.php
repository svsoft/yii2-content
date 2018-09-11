<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m180215_130215_add_fields_table_content_property extends Migration
{
    public function safeUp()
    {
        $property = 'svs_content_property';
        $this->addColumn($property, 'label', $this->string()->notNull());
        $this->update($property,['label'=>new \yii\db\Expression('name')]);
        $this->dropColumn($property, 'slug');
    }

    public function safeDown()
    {
        $property = 'svs_content_property';
        $this->dropColumn($property, 'label');
        $this->addColumn($property, 'slug', $this->string());

        return true;
    }
}
