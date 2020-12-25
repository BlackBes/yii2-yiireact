<?php

namespace blackbes\yiireact\controllers;

use app\models\Linen;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use blackbes\yiireact\BaseController;
/**
 * ValidateController implements the actions that helps provide validations.
 */
class ValidateController extends BaseController {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return array_merge(parent::behaviors(), [
            // For cross-domain AJAX request
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    'Origin' => static::allowedDomains(),
                    'Access-Control-Request-Method'    => ['POST'],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Allow-Headers' => ['*'],
                    'Access-Control-Max-Age'           => 3600
                ],
            ],
            'access'        => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Handles validation of model inputs. Returns true or validation warning
     * Params must be sent via POST:
     *      model - model name
     *      name - model field name
     *      value - model field value
     * @return mixed
     */
    public function actionModelInput() {
        $model = Yii::$app->request->post('model');
        if(!preg_match('/[A-Z]/', $model)){
            $model = ucfirst($model);
        }
        $name = Yii::$app->request->post('name');
        $value = Yii::$app->request->post('value');

        if(!empty($model)) {
            if (class_exists($this->models_namespace . $model)) {
                $models_full_path = $this->models_namespace . $model;

                $mod = new $models_full_path();
                $mod->$name = $value;

                $mod->validate();
                $errors = $mod->getErrors();

                if (!isset($errors[$name])) {
                    return json_encode(['data' => true], JSON_UNESCAPED_UNICODE);
                } else {
                    $error = $errors[$name][0];
                    return json_encode(['error' => $error], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return json_encode(['error' => 'There is no '.$model.' model.'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return json_encode(['error' => 'Model can`t be empty.'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
}
