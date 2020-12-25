<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

?>
import React, {Component} from 'react';
import axios from "axios";
import stringify from "qs-stringify";
import {connect} from "react-redux";
import { InputField, CheckBox } from "react-yii2-essentials";
import {Redirect} from 'react-router-dom';
import {prepareForm} from "../../functions/forms";
import { IndexDataLoader } from "react-yii2-essentials";

class _form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            model: '<?= StringHelper::basename($generator->modelClass) ?>',
            validation: '',
            form: {
<?php
            $tableSchema = $generator->getTableSchema();
            foreach ($tableSchema->columns as $column) {
                echo "                " . $column->name . ": ''". ",\n";
             } ?>
            },
            redirect: false,
            id: '',
            isLoaded:false,
        };

        this.saveForm = this.saveForm.bind(this);
        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        if (this.props.hasOwnProperty('data')) {
            this.setState({form: this.props.data});
        }
    }

    saveForm = async (data) => {
        let this_el = this;
        await axios({
            method: 'post',
            url: this.props.api.address + this.props.action,
            data: stringify({
                data
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization': 'Bearer ' + this_el.props.api.authToken
            }
        }).then(function (response) {
            if (response.data !== '' && response.data.constructor === Object) {
                let event = response.data;

                if (!('error' in event)) {
                    this_el.setState({validation: ''});
                    this_el.setState({id: event.data});
                    this_el.setState({redirect: true});
                } else {
                    this_el.setState({validation: event.error});
                    console.log(event.data);
                }
            } else {
                console.log('Error while fetching events data!');
            }
        }).catch(function (error) {
            console.log(error.message);
        });
    };

    handleSubmit = (event) => {
        event.preventDefault();
        let data = prepareForm(this.state.model, this.state.form);
        this.saveForm(data);
    }

    handleInputChange(event) {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        let form = this.state.form;
        form[name] = value;

        this.setState({
            form: form
        });
    }

    render() {
        if (this.state.redirect) {
            return <Redirect to={"/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/view/" + this.state.id}/>;
        }
        return (
        <form onSubmit={this.handleSubmit}>
            <div className={"flex-form"}>
                <?php
                $tableSchema = $generator->getTableSchema();
                foreach ($tableSchema->columns as $column) {
                    if($column->phpType !== 'boolean'){
                        ?>
                        <InputField name={'<?= $column->name?>'} model={this.state.model} label={''} class={'main-input'}
                                    placeholder={""} onChange={this.handleInputChange} value={this.state.form['<?= $column->name?>']} />
                    <?php } else {
                    ?>
                        <CheckBox name={'<?= $column->name?>'} model={this.state.model} label={''} class={'main-input'}
                                placeholder={""} key = {'<?= $column->name?>'} value={this.state.form['<?= $column->name?>']} />
                <?php }
                }
                ?>
                <div className={'form-validation'}>{this.state.validation}</div>
            </div>

            <div align={"center"}>
                <button type={"submit"} className={'btn btn-success'}>Save</button>
            </div>
        </form>
        );
    }
}

const mapStateToProps = state => ({
    api: state.api
});

const mapDispatchToProps = dispatch => ({});

export default connect(mapStateToProps, mapDispatchToProps)(_form);
