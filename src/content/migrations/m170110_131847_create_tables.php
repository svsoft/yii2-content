<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m170110_131847_create_tables extends Migration
{
    public function safeUp()
    {

        $typeTable = 'svs_content_type';
        $propertyTable = 'svs_content_property';

        // Таблица типов моделей к которым можно привязать свойста
        $this->createTable($typeTable, [
            'type_id'     => $this->primaryKey()->unsigned(),
            'name'        => $this->string()->notNull(),
            'value_field' => $this->string()->notNull(),
            'simple'      => $this->boolean()->defaultValue(true)->notNull(),
            'classname'   => $this->string(),
            'slug'        => $this->string()
        ]);

        $this->createTable($propertyTable, [
            'property_id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'parent_type_id' => $this->integer()->unsigned()->notNull(),
            'property_type_id' => $this->integer()->unsigned()->notNull(),
            'multiple' => $this->boolean()->defaultValue(false)->notNull(),
            'slug'      => $this->string()
        ]);

        $this->addForeignKey($propertyTable . '_parent_type_id', $propertyTable, 'parent_type_id', $typeTable, 'type_id', 'RESTRICT');
        $this->addForeignKey($propertyTable . '_property_type_id', $propertyTable, 'property_type_id', $typeTable, 'type_id', 'RESTRICT');

        $this->insert($typeTable, ['name'=>'Строка', 'simple'=>1, 'value_field'=>'value_string', 'slug'=>'string']);
        $this->insert($typeTable, ['name'=>'Число', 'simple'=>1, 'value_field'=>'value_float', 'slug'=>'float']);
        $this->insert($typeTable, ['name'=>'Файл', 'simple'=>1, 'value_field'=>'value_string', 'slug'=>'file']);
        $this->insert($typeTable, ['name'=>'Текст', 'simple'=>1, 'value_field'=>'value_text', 'slug'=>'text']);
        $this->insert($typeTable, ['name'=>'Html', 'simple'=>1, 'value_field'=>'value_text', 'slug'=>'html']);
    }

    public function safeDown()
    {
        $this->dropTable('svs_content_property');
        $this->dropTable('svs_content_type');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170610_113847_ctreate_tables cannot be reverted.\n";

        return false;
    }
    */
}
