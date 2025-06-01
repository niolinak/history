<?php

namespace backend\modules\history;
use Yii;
use yii\base\BootstrapInterface;

/**
 * module definition class
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'backend\modules\history\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $app->params['dee.migration.path'][] = '@backend/modules/history/migrations';
        }
    }
}
