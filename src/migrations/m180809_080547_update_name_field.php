<?php
namespace svsoft\yii\content\migrations;

use yii\db\Migration;

class m180809_080547_update_name_field extends Migration
{
    public function safeUp()
    {
        $typeTable = 'svs_content_type';

        $this->update($typeTable,['name'=>'string'],['name'=>'Строка']);
        $this->update($typeTable,['name'=>'float'],['name'=>'Число']);
        $this->update($typeTable,['name'=>'file'],['name'=>'Файл']);
        $this->update($typeTable,['name'=>'text'],['name'=>'Текст']);
        $this->update($typeTable,['name'=>'html'],['name'=>'Html']);
    }

    public function safeDown()
    {
        return true;
    }
}
