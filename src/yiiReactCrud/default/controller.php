<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends  <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex() {
        $model = new <?= $modelClass ?>();
        $data = $model->getProviderData();

        return json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>) {
        $model = <?= $modelClass ?>::findOne($id);
        $model_data = $model->getData();

        return json_encode(['data' => $model_data], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * @return mixed
     */
    public function actionCreate() {
        $model = new <?= $modelClass ?>();

        if (array_key_exists('data', Yii::$app->request->post())) {
            $data_to_load = Yii::$app->request->post()['data'];

            if ($model->load($data_to_load)) {
                if ($model->save()) {
                    return json_encode(['data' => $model->id], JSON_UNESCAPED_UNICODE);
               } else {
                   return json_encode([
                       'error' => 'Check the errors above',
                       'data'  => $model->getErrors()
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return json_encode(Yii::$app->request->post(), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = <?= $modelClass ?>::findOne($id);
        $model_data = $model->getData();

        if (array_key_exists('data', Yii::$app->request->post())) {
            $data_to_load = Yii::$app->request->post()['data'];

            if ($model->load($data_to_load)) {
                if ($model->save()) {
                    return json_encode(['data' => $model->id], JSON_UNESCAPED_UNICODE);
                } else {
                    return json_encode([
                        'error' => 'Check the errors above',
                        'data'  => $model->getErrors()
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        }
    return json_encode(['data' => $model_data], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete($id) {
        <?= $modelClass ?>::findOne($id)
            ->delete();

        return json_encode(['data' => true], JSON_UNESCAPED_UNICODE);
    }
}
