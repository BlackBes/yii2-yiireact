<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

?>
import React, {Component} from 'react';
import {connect} from "react-redux";
import Form from "./_form";
import '../../App.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import { BreadCrumbs } from "react-yii2-essentials";
import { setBreadcrumbs } from "../../actions";

class create extends Component {
    constructor(props) {
        super(props);

        let bread = [
            {
                name: 'Home',
                link: '/'
            },
            {
                name: '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>',
                link: '/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>'
            },
            {
                name: "Create",
                link: ''
            },
        ]
        this.props.setBreadcrumbs(bread);
    }

    render() {
        return (
            <div className={'container white-block'}>
                    <h1>Create <?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?></h1>
                    <Form action={'/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/create'} data={{}}/>
            </div>
        );
    }
}

const mapStateToProps = state => ({
    api: state.api,
    breadcrumbs: state.breadcrumbs
});

const mapDispatchToProps = dispatch => ({
    setBreadcrumbs: breadcrumb => dispatch(setBreadcrumbs(breadcrumb))
});

export default connect(mapStateToProps, mapDispatchToProps)(create);
