<?php

namespace blackbes\yiireact;

use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\Url;

class Bootstrap implements BootstrapInterface {
    /**
     * Bootstrapping module and create route for valigation.
     *
     * @param $app
     */
    public function bootstrap($app) {
        $app->setModule('yiireact', ['class' => 'blackbes\yiireact\Module']);

        $app->getUrlManager()
            ->addRules([
                'api/validate-model-input' => 'yiireact/validate/model-input',
            ], false);
    }
}