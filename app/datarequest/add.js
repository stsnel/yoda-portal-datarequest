import React, { Component } from "react";
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import BootstrapTable from 'react-bootstrap-table-next';
import filterFactory, { numberFilter, textFilter, selectFilter, multiSelectFilter, Comparator } from 'react-bootstrap-table2-filter';
import paginationFactory from 'react-bootstrap-table2-paginator';
import DataSelection, { DataSelectionTable } from "./DataSelection.js";

document.addEventListener("DOMContentLoaded", async () => {

    // Get data request schema and uiSchema
    fetch("/datarequest/datarequest/schema")
    .then(async response => {
        let schemas = await response.json();

        let datarequestSchema = schemas.schema;
        let datarequestUiSchema = schemas.uiSchema;

        // If specified, get data of previous data request (of which the present
        // data request will become a resubmission) and prefill data request
        // form
        if (typeof previousRequestId !== 'undefined') {
            fetch("/datarequest/datarequest/data/" + previousRequestId)
            .then(async response => {
                let datarequestFormData = await response.json();

                render(<Container schema={datarequestSchema}
                                  uiSchema={datarequestUiSchema}
                                  formData={datarequestFormData} />,
                       document.getElementById("form"));
            });
        // Else, render blank data request form
        } else {
            render(<Container schema={datarequestSchema}
                              uiSchema={datarequestUiSchema} />,
                   document.getElementById("form"));
        }
    });
});

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
          <YodaForm schema={this.props.schema}
                    uiSchema={this.props.uiSchema}
                    formData={this.props.formData}
                    ref={(form) => {this.form=form;}}/>
          <YodaButtons submitButton={this.submitForm}/>
        </div>
      );
    }
};

class YodaForm extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Form className="form"
                  schema={this.props.schema}
                  idPrefix={"yoda"}
                  uiSchema={this.props.uiSchema}
                  formData={this.props.formData}
                  fields={fields}
                  onSubmit={onSubmit}>
                  <button ref={(btn) => {this.submitButton=btn;}}
                          className="hidden" />
            </Form>
        );
    }
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

const onSubmit = ({formData}) => submitData(formData);

const CustomDescriptionField = ({id, description}) => {
  return <div id={id} dangerouslySetInnerHTML={{ __html: description }}></div>;
};

const fields = {
  DescriptionField: CustomDescriptionField,
  DataSelection: DataSelectionTable
};

function submitData(data)
{
    // Disable submit button
    $("button:submit").attr("disabled", "disabled");

    // Submit form and redirect to overview page
    Yoda.call("datarequest_submit",
        {data: data,
         previous_request_id: typeof(previousRequestId) !== 'undefined' ? previousRequestId : null},
        {errorPrefix: "Could not submit data"})
    .then(() => {
        window.location.href = "/datarequest/";
    })
    .catch(error => {
        // Re-enable submit button if submission failed
        $('button:submit').attr("disabled", false);
    });
}
