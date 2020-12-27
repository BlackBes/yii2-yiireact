<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
?>
import React, {Component} from 'react';
import {connect} from "react-redux";
import '../../App.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import {Link, Redirect} from "react-router-dom";
import "react-loader-spinner/dist/loader/css/react-spinner-loader.css";
import {setBreadcrumbs} from "../../redux/actions";
import {DataView, PrepareIndexModal, FetchIndexData, ManipulateIndexData, IndexDataLoader} from "react-yii2-essentials";

class index extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {},
            page: 1,
            modal: false,
            modalAction: 'delete',
            modelToUse: '',
            isDataLoaded: false,
            hasError: false
    }

    const {t} = this.props;

    const bread = [
        {
            name: 'Home',
            link: '/'
            },
            {
            name: '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>',
            link: ''
        }
    ]
    this.props.setBreadcrumbs(bread)

    this.actionPagination = this.actionPagination.bind(this);
    this.actionOpenModal = this.actionOpenModal.bind(this);
    this.actionCloseModal = this.actionCloseModal.bind(this);
}

    componentDidMount() {
        FetchIndexData(this.props.api,'<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', this.state.page, (response) => this.onDataFetched(response));
    }

    onDataFetched(response) {
        let this_el = this;
        let event = response.data;
        return {
            onSuccess: () => {
                this_el.setState({data: event.data});
                this_el.setState({isDataLoaded: true});
            },
            onError: () => {
                console.log(response);
                this_el.setState({ hasError: true}) ;
            }
        }
    }

    actionPagination(id) {
        this.setState({page: id});
        this.setState({isDataLoaded: false});
        FetchIndexData(this.props.api,'<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', id, (response) => this.onDataFetched(response));
    }

    actionCloseModal() {
        this.setState({modal: false});
        this.setState({modelToUse: ''});
    }

    actionOpenModal(id, type) {
        this.setState({modelToUse: id});
        this.setState({modal: true});
        this.setState({modalAction: type});
    }

    onManipulateIndexData(response) {
        let this_el = this;
        return {
            onSuccess: () => {
                FetchIndexData(this_el.props.api,'<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', this_el.state.page, (response) => this_el.onDataFetched(response));
            this_el.actionCloseModal();
        },
            onError: () => {
                console.log(response);
                this_el.setState({ hasError: true}) ;
            }
        }
    }

    modalCallbacks() {
        return {
            onClose: this.actionCloseModal,
            onDelete: () => ManipulateIndexData(this.props.api, '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', 'delete', this.state.modelToUse, (response) => this.onManipulateIndexData(response)),
            onRestore: () => ManipulateIndexData(this.props.api, '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>', 'restore', this.state.modelToUse, (response) => this.onManipulateIndexData(response)),
        }
    }

    render() {
        if (this.state.redirect) {
            return <Redirect to={"/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>"}/>;
        }

        //change this function to 'const {t} = this.props' when you have i18n translation enabled
        const t = (data) => {return data}

        return (
            <div>
                {PrepareIndexModal(this.modalCallbacks(), this.state.modal, this.state.modalAction)}
                <div className={'index-page-header'}>
                    <h1><?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?></h1>
                    <div className={'model-actions'}>
                        <Link className="btn btn-success" to={'/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/create'}>Create</Link>
                    </div>
                </div>

                {
                    (this.state.isDataLoaded) ?
                    <DataView
                        models={this.state.data.data}
                        modelName={"<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>"}
                        className={""}
                        attributes={{
                            <?php
                                $count = 0;
                                if (($tableSchema = $generator->getTableSchema()) === false) {
                                    foreach ($generator->getColumnNames() as $name) {
                                        if (++$count < 6) {
                                            echo $name .":"."'" . $name . "',\n";
                                        } else {
                                            echo "//'" . $name .":"."'" . $name . "',\n";
                                        }
                                    }
                                } else {
                                    foreach ($tableSchema->columns as $column) {
                                        $format = $generator->generateColumnFormat($column);
                                        if (++$count < 6) {
                                            echo $column->name .":"."'" . $column->name . "',\n";
                                        } else {
                                            echo "//'" . $column->name .":"."'" . $column->name . "',\n";
                                        }
                                    }
                                }
                            ?>
                        }}
                        actions={{
                            update: {title: "update"},
                            delete: {title: "delete"},
                            restore: {title: "restore"},
                        }}
                        modalActions={{
                            onModalOpen: this.actionOpenModal,
                        }}
                        ifEmpty={"Create first element"}
                        pagination={{
                            currentPage: this.state.page,
                            totalPages: this.state.data.total_pages,
                            callback: this.actionPagination
                        }}
                    />
                    : <IndexDataLoader />
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

export default connect(mapStateToProps, mapDispatchToProps)(index);

