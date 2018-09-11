<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m170110_164032_create_item_tables extends Migration
{
    public function safeUp()
    {

        $typeTable = 'svs_content_type';
        $propertyTable = 'svs_content_property';
        $itemTable = 'svs_content_item';
        $valueTable = 'svs_content_value';

        // Таблица типов моделей к которым можно привязать свойста
        $this->createTable($itemTable, [
            'item_id'   => $this->primaryKey()->unsigned(),
            'type_id' => $this->integer()->unsigned()->notNull(),
            'name'      => $this->string(),
            'slug'      => $this->string()
        ]);

        $this->createTable($valueTable, [
            'value_id' => $this->primaryKey()->unsigned(),
            'item_id' => $this->integer()->unsigned()->notNull(),
            'property_id' => $this->integer()->unsigned()->notNull(),
            'value_item_id' => $this->integer()->unsigned(),
            'value_string' => $this->string(),
            'value_text' => $this->text(),
            'value_int' => $this->integer(),
            'value_float' => $this->float(),
        ]);

        $this->addForeignKey($itemTable . '_type_id', $itemTable, 'type_id', $typeTable, 'type_id', 'RESTRICT');
        $this->addForeignKey($valueTable . '_item_id', $valueTable, 'item_id', $itemTable, 'item_id', 'RESTRICT');
        $this->addForeignKey($valueTable . '_value_item_id', $valueTable, 'value_item_id', $itemTable, 'item_id', 'RESTRICT');
        $this->addForeignKey($valueTable . '_property_id', $valueTable, 'property_id', $propertyTable, 'property_id', 'RESTRICT');

    }

    public function safeDown()
    {
        $this->dropTable('svs_content_value');
        $this->dropTable('svs_content_item');

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
