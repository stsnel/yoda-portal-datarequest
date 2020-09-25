import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import DataSelection, { DataSelectionCart } from "./DataSelection.js";

$(document).ready(function() {

    var datarequestSchema   = {};
    var datarequestUiSchema = {};
    var datarequestFormData = {};

    // Get data request
    Yoda.call('datarequest_get',
        {request_id: requestId},
        {errorPrefix: "Could not get datarequest"})
    .then((datarequest) => {
        datarequestFormData = JSON.parse(datarequest.requestJSON);
    })
    // Get data request schema and uiSchema
    .then(async function() {
        let schema = await axios.get("/datarequest/datarequest/schema");
        datarequestSchema   = schema.data.schema;
        datarequestUiSchema = schema.data.uiSchema;
    })
    // Render data request as disabled form
    .then(() => {
        render(<ContainerReadonly schema={datarequestSchema}
                                  uiSchema={datarequestUiSchema}
                                  formData={datarequestFormData} />,
               document.getElementById("datarequest")
        );
    });

    var prSchema   = {};
    var prUiSchema = {};
    var prFormData = {};

    // Get preliminary review
    Yoda.call('datarequest_preliminary_review_get',
        {request_id: requestId},
        {errorPrefix: "Could not get preliminary review"})
    .then((preliminary_review) => {
        prFormData = JSON.parse(preliminary_review);
    })
    // Get preliminary review schema and uiSchema
    .then(async function() {
        let schema = await axios.get("/datarequest/datarequest/preliminaryReviewSchema");
        prSchema   = schema.data.schema;
        prUiSchema = schema.data.uiSchema;
    })
    // Render preliminary review as disabled form
    .then(() => {
        render(<ContainerReadonly schema={prSchema}
                                  uiSchema={prUiSchema}
                                  formData={prFormData} />,
            document.getElementById("preliminaryReview")
        );
    });

    var datamanagerReviewSchema = {};
    var datamanagerReviewUiSchema = {};
    var form = document.getElementById('datamanagerReview');

    // Get the schema of the data request review form for the data manager
    axios.get("/datarequest/datarequest/datamanagerReviewSchema")
    .then(function (response) {
        datamanagerReviewSchema = response.data.schema;
        datamanagerReviewUiSchema = response.data.uiSchema;

        render(<Container schema={datamanagerReviewSchema}
                          uiSchema={datamanagerReviewUiSchema} />,
            document.getElementById("datamanagerReview")
        );
    });
});

class YodaForm extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Form className="form"
                  schema={this.props.schema}
                  uiSchema={this.props.uiSchema}
                  idPrefix={"yoda"}
                  onSubmit={onSubmit}>
                  <button ref={(btn) => {this.submitButton=btn;}}
                          className="hidden" />
            </Form>
        );
    }
};

class YodaFormReadonly extends React.Component {
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
                  disabled>
                  <button className="hidden" />
            </Form>
        );
    }
};

const CustomDescriptionField = ({id, description}) => {
  return <div id={id} dangerouslySetInnerHTML={{ __html: description }}></div>;
};

const fields = {
  DescriptionField: CustomDescriptionField,
  DataSelection: DataSelectionCart
};

const onSubmit = ({formData}) => submitData(formData);

class YodaButtons extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="form-group">
                <div className="row yodaButtons">
                    <div className="col-sm-12">
                        <button onClick={this.props.submitButton}
                                type="submit"
                                className="btn btn-primary">Submit</button>
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
          <YodaForm schema={this.props.schema}
                    uiSchema={this.props.uiSchema}
                    ref={(form) => {this.form=form;}}/>
          <YodaButtons submitButton={this.submitForm}/>
        </div>
        );
    }
}

class ContainerReadonly extends React.Component {
    render() {
        return (
        <div>
          <YodaFormReadonly schema={this.props.schema}
                            uiSchema={this.props.uiSchema}
                            formData={this.props.formData} />
        </div>
      );
    }
}

async function submitData(data) {

    // Disable submit button
    $("button:submit").attr("disabled", true);

    // Store
    Yoda.call("datarequest_datamanager_review_submit",
        {data: JSON.stringify(data),
         request_id: requestId},
        {errorPrefix: "Could not submit datamanager review"})
    .then(function (response) {
        window.location.href = "/datarequest/view/" + requestId;
    })
    .catch((error) => {
        $("button:submit").attr("disabled", false);
    });
}
