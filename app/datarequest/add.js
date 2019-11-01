import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import BootstrapTable from 'react-bootstrap-table-next';
import filterFactory, { numberFilter, textFilter, selectFilter, multiSelectFilter, Comparator } from 'react-bootstrap-table2-filter';
import paginationFactory from 'react-bootstrap-table2-paginator';
import DataSelection, { DataSelectionTable } from "./DataSelection.js";

var schema = {};
var uiSchema = {};
var formData = {};

var form = document.getElementById('form');

// Get schema
axios.get("/datarequest/datarequest/schema")
    .then(function (response) {
        schema = response.data.schema;
        uiSchema = response.data.uiSchema;

        // Get schema of previous data request (of which this data request will become a resubmission) if specified
        if (typeof previousRequestId !== 'undefined') {
            axios.get("/datarequest/datarequest/data/" + previousRequestId)
                .then(function(response) {
                    formData = response.data;
                    render(<Container formData={formData} />,
                        document.getElementById("form")
                    );
                });
        } else {
            render(<Container />,
                document.getElementById("form")
            );
        }

    });

const onSubmit = ({formData}) => submitData(formData);


class YodaForm extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Form className="form"
                  schema={schema}
                  idPrefix={"yoda"}
                  uiSchema={uiSchema}
                  formData={formData}
                  fields={fields}
                  onSubmit={onSubmit}>
                <button ref={(btn) => {this.submitButton=btn;}} className="hidden" />
            </Form>
        );
    }
};

const CustomDescriptionField = ({id, description}) => {
  return <div id={id} dangerouslySetInnerHTML={{ __html: description }}></div>;
};

const fields = {
  DescriptionField: CustomDescriptionField,
  DataSelection: DataSelectionTable
};

class YodaButtons extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="form-group">
                <div className="row yodaButtons">
                    <div className="col-sm-12">
                        <button onClick={this.props.submitButton} type="submit" className="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        );
    }
}

class Container extends React.Component {
    constructor(props) {
        super(props);
        this.submitForm = this.submitForm.bind(this);
    }

    submitForm() {
        this.form.submitButton.click();
    }

    render() {
        return (
        <div>
          <YodaForm formData={this.props.formData} ref={(form) => {this.form=form;}}/>
          <YodaButtons submitButton={this.submitForm}/>
        </div>
      );
    }
};

function submitData(data)
{
    // Disable submit button
    $("button:submit").attr("disabled", "disabled");

    var tokenName = form.dataset.csrf_token_name;
    var tokenHash = form.dataset.csrf_token_hash;

    // Create form data.
    var bodyFormData = new FormData();
    bodyFormData.set(tokenName, tokenHash);
    bodyFormData.set('formData', JSON.stringify(data));

    // If set, append previous_request_id to POST data
    if (typeof(previousRequestId) !== 'undefined') {
        bodyFormData.set('previousRequestId', previousRequestId);
    }

    // Store.
    axios({
        method: 'post',
        url: "/datarequest/datarequest/store",
        data: bodyFormData,
        config: { headers: {'Content-Type': 'multipart/form-data' }}
        })
        .then(function (response) {
            window.location.href = "/datarequest";
        })
        .catch(function (error) {
            //handle error
            console.log('ERROR:');
            console.log(error);
        });
}
