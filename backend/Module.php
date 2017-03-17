<?php

namespace abcms\shop\backend;

/**
 * shop-backend module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'abcms\shop\backend\controllers';
    
    /**
     * @var string Gallery module base route
     */
    public $galleryRoute = '/admin/gallery';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
