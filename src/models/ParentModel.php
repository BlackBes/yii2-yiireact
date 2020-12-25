<?php


namespace blackbes\yiireact\models;

use Yii;

class ParentModel extends \yii\db\ActiveRecord
{
    public $page_limit = 10;

    /**
     * Removes all special characters in the specified model attribute
     * @param string $attribute Attribute name
     * @return mixed
     */
    public function purifyAttribute($attribute){
        if ($this->hasAttribute($attribute)){
            $value = $this[$attribute];
            $value = str_replace(array('[',']','<','>','/','|','*','~','_',), '',$value);
            $this[$attribute] = $value;
            return true;
        } else {
            Yii::$app->response->statusCode = 404;
            return 'Error! Attribute not found';
        }
    }

    public function getAllAttributes() {
        return array_merge(
            parent::attributes(),
            \yii\base\Model::attributes()
        );
    }

    /**
     * Gets all model data
     * @return array
     */
    function getData() {
        $model_data = [];
        $attributes = $this->getAllAttributes();
        $types = $this->getValidators();
        $booleans = [];
        foreach ($types as $type) {
            if($type instanceof yii\validators\BooleanValidator) {
                $booleans = $type->attributes;
            }
        }

        foreach ($attributes as $attribute) {
            $value = $this->$attribute;
            if(in_array($attribute, $booleans)) {
                if($value == 1) {
                    $value = true;
                } else {
                    $value = false;
                }
            }
            $model_data[$attribute] = $value;
        }

        return $model_data;
    }

    /**
     * Gets all model data for index page
     * @param bool $isUsers - is used for index page on users, so the first user (superAdmin) will not be shown in list
     * @return array
     */
    public function getProviderData($isUsers = false) {
        $page = Yii::$app->request->get('page');
        if(empty($page)) {
            $page = 1;
        }
        $page -= 1;

        if ($isUsers){
            $models = static::find()
                ->andWhere(["!=","id",1])
                ->limit($this->page_limit)
                ->offset($this->page_limit * $page)
                ->orderBy([
                    'created_at' => SORT_DESC,
                ])
                ->all();
        } else {
            $models = static::find()
                ->limit($this->page_limit)
                ->offset($this->page_limit * $page)
                ->orderBy([
                    'created_at' => SORT_DESC,
                ])
                ->all();
        }

        $total_models = static::find()
            ->count();
        $total_pages = intval(abs($total_models / $this->page_limit));

        if($total_models % $this->page_limit > 0) {
            $total_pages++;
        }

        $models_data = [];

        foreach ($models as $model) {
            $models_data[] = $model->getData();
        }

        return ['data' => $models_data, 'current_page' => $page, 'total_pages' => $total_pages];
    }
}