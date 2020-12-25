<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
?>
//Put this in imports section
import <?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>Create from './containers/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/create';
import <?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>Update from './containers/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/update';
import <?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>View from './containers/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/view';
import <?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>Index from './containers/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/index';

//Put this in <Switch> component in render function
    <PrivateRoute exact path='/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>' component={<?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>Index} />
    <PrivateRoute exact path='/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/create' component={<?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>Create} />
    <PrivateRoute exact path='/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/update/:id' component={<?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>Update} />
    <PrivateRoute exact path='/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/view/:id' component={<?= Inflector::camelize(StringHelper::basename($generator->modelClass)) ?>View} />
