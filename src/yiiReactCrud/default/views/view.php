<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

?>
import React, {Component} from 'react';
import axios from "axios";
import stringify from "qs-stringify";
import {connect} from "react-redux";
import '../../App.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import {Link, Redirect} from "react-router-dom";
import "react-loader-spinner/dist/loader/css/react-spinner-loader.css";
import { DataTable } from "react-yii2-essentials";
import {setBreadcrumbs} from "../../actions";
import { BreadCrumbs } from "react-yii2-essentials";
import Modal from "react-bootstrap4-modal";

class view extends Component {
    constructor(props) {
        super(props);

        this.state = {
            model: {},
            isDataLoaded: false,
            redirect: false,
            modal: false,
            modelToDelete: '',
            hasError: false
        }

        this.fetchData = this.fetchData.bind(this);
        this.deleteModel = this.deleteModel.bind(this);
        this.openModal = this.openModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
    }

    componentDidMount() {
        const  id  = this.props.match.params.id;
        this.fetchData(id);
    }

    fetchData = async (id) => {
        let this_el = this;
        await axios({
            method: 'post',
            url: this.props.api.address+"/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/view?id="+id,
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
                        link: ''
                    },
                ]
                this_el.props.setBreadcrumbs(bread);
                this_el.setState({isDataLoaded: true});
            } else {
                console.log('Error while fetching events data!');
            }
        }).catch(function (error) {
            this_el.state.hasError = false;
            console.log(error.message);
        });
    };

    deleteModel = async () => {
        let id = this.state.modelToDelete;
        let this_el = this;

        await axios({
            method: 'post',
            url: this.props.api.address+"/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/delete?id="+id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization': 'Bearer '+this_el.props.api.authToken
            }
        }).then(function (response) {
            if (response.data !== '' && response.data.constructor === Object) {
                this_el.setState({redirect: true});
            } else {
                console.log('Error while fetching events data!');
            }
        }).catch(function (error) {
            console.log(error.message);
        });
    }

    closeModal() {
        this.setState({modal: false});
        this.setState({modelToDelete: ''});
    }

    openModal(id) {
        this.setState({modelToDelete: id});
        this.setState({modal: true});
    }

    render() {
        if (this.state.redirect) {
            return <Redirect to={"/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>"} />;
        }
        return (
            <div className={'container white-block'}>
                <Modal visible={this.state.modal} onClickBackdrop={this.closeModal}>
                    <div className="modal-header">
                        <h5 className="modal-title">Deleting</h5>
                    </div>
                    <div className="modal-body">
                        <p>Are you sure you want to delete this item?</p>
                    </div>
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" onClick={this.closeModal}>
                            Close
                        </button>
                        <button type="button" className="btn btn-danger" onClick={this.deleteModel}>
                            Delete
                        </button>
                    </div>
                </Modal>
                    <h1>{this.state.model.name}</h1>
                    <div className={'model-actions'}>
                        <Link className="btn btn-primary" to={'/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/update/' + this.state.model.id}>Update</Link>
                        <button className="btn btn-danger" onClick={() => this.openModal(this.state.model.id)}>Delete</button>
                    </div>

                    <DataTable model={this.state.model} fields={[
<?php
                        $count = 0;
                        if (($tableSchema = $generator->getTableSchema()) === false) {
                            foreach ($generator->getColumnNames() as $name) {
                                if (++$count < 6) {
                                    echo "                            '" . $name . "',\n";
                                } else {
                                    echo "                            //'" . $name . "',\n";
                                }
                            }
                        } else {
                            foreach ($tableSchema->columns as $column) {
                                $format = $generator->generateColumnFormat($column);
                                if (++$count < 6) {
                                    echo "                            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                                } else {
                                    echo "                            //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                                }
                            }
                        }
                        ?>
                               ]} />
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

export default connect(mapStateToProps, mapDispatchToProps)(view);
