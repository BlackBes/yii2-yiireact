<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$modelClassName = Inflector::camel2words(StringHelper::basename($generator->modelClass));
?>
import React, {Component} from 'react';
import axios from "axios";
import stringify from "qs-stringify";
import {connect} from "react-redux";
import Form from "./_form";
import '../../App.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import "react-loader-spinner/dist/loader/css/react-spinner-loader.css";
import {setBreadcrumbs} from "../../actions";
import {BreadCrumbs, IndexDataLoader} from "react-yii2-essentials";

class update extends Component {
    constructor(props) {
        super(props);

        this.state = {
            model: {},
            isDataLoaded: false
        }

        this.fetchData = this.fetchData.bind(this);
    }

    componentDidMount() {
        const id = this.props.match.params.id;
        this.fetchData(id);
    }

    fetchData = async (id) => {
        let this_el = this;
        await axios({
            method: 'post',
            url: this.props.api.address + "/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/update?id=" + id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization': 'Bearer '+this_el.props.api.authToken
            }
        }).then(function (response) {
            if (response.data !== '' && response.data.constructor === Object) {
                let event = response.data;
                this_el.setState({model: event.data});

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
                        name: event.data.name,
                        link: '/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/view/'+event.data.id
                    },
                    {
                        name: "Update",
                        link: ''
                    },
                ]
                this_el.props.setBreadcrumbs(bread);
                this_el.setState({isDataLoaded: true});
            } else {
                console.log('Error while fetching events data!');
            }
        }).catch(function (error) {
            console.log(error.message);
        });
    };

    render() {

    return (
    <div className={'container white-block'}>
            <h1>Update <?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>: {this.state.model.name}</h1>
            {(this.state.isDataLoaded) ?
            <Form action={'/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/update?id=' + this.state.model.id} data={this.state.model }/>
                :
                <IndexDataLoader/>
                }
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

export default connect(mapStateToProps, mapDispatchToProps)(update);
