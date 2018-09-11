<?php

namespace svsoft\yii\content\admin;

use svsoft\yii\admin\base\AdminModuleBase;

/**
 * admin module definition class
 */
class ContentAdminModule extends AdminModuleBase
{

    public $menu;

    const MENU_MODE_LIST = 1;
    const MENU_MODE_DETAIL = 2;

    /**
     * @inheritdoc
     */
    //public $controllerNamespace = 'svsoft\yii\modules\catalog\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

}
