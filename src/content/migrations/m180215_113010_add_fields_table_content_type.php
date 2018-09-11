<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m180215_113010_add_fields_table_content_type extends Migration
{
    public function safeUp()
    {
        $type = 'svs_content_type';
        $this->addColumn($type, 'label', $this->string()->notNull());
        $this->update($type,['label'=>new \yii\db\Expression('name')]);
        $this->dropColumn($type, 'slug');
    }

    public function safeDown()
    {
        $type = 'svs_content_type';
        $this->dropColumn($type, 'label');
        $this->addColumn($type, 'slug', $this->string());

        return true;
    }

}
