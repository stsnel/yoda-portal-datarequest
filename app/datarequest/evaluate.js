import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form";

var datarequestSchema = {};
var datarequestUiSchema = {};
var datarequestFormData = {};

// Get the schema, uiSchema and formData of the data request to be evaluated.
// Then render the data as a disabled form
axios.all([
    axios.get("/datarequest/datarequest/schema"),
    axios.get("/datarequest/datarequest/data/" + requestId)
    ])
    .then(axios.spread((schemaresponse, dataresponse) => {
        datarequestFormData = dataresponse.data;
        datarequestSchema   = schemaresponse.data.schema;
        datarequestUiSchema = schemaresponse.data.uiSchema;

        render(<ContainerReadonly schema={datarequestSchema}
                                  uiSchema={datarequestUiSchema}
                                  formData={datarequestFormData} />,
            document.getElementById("datarequest")
        );
    }));

var reviewSchema = {};
var reviewUiSchema = {};
var reviewFormData = {};

// Get the schema and formData of the data request review. Then render the data
// as a disabled form
axios.all([
    axios.get("/datarequest/datarequest/reviewSchema"),
    axios.get("/datarequest/datarequest/reviewData/" + requestId)
    ])
    .then(axios.spread((schemaresponse, dataresponse) => {
        console.log(dataresponse);
        reviewFormData = dataresponse.data;
        reviewSchema   = schemaresponse.data.schema;

        render(<ContainerReadonly schema={reviewSchema}
                                  uiSchema={reviewUiSchema}
                                  formData={reviewFormData} />,
            document.getElementById("review")
        );
    }));

var evaluationSchema = {};
var evaluationUiSchema = {};
var form = document.getElementById('evaluation');

// Get the schema of the data request evaluation form
axios.get("/datarequest/datarequest/evaluationSchema")
    .then(function (response) {
        console.log(response);
        evaluationSchema = response.data.schema;
        evaluationUiSchema = response.data.uiSchema;

        render(<Container schema={evaluationSchema}
                          uiSchema={evaluationUiSchema} />,
            document.getElementById("evaluation")
        );
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
                  disabled>
                  <button className="hidden" />
            </Form>
        );
    }
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
    bodyFormData.set('requestId', requestId);

   // Store.
    axios({
        method: 'post',
        url: "/datarequest/datarequest/store_evaluation",
        data: bodyFormData,
        config: { headers: {'Content-Type': 'multipart/form-data' }}
        })
        .then(function (response) {
            window.location.href = "/datarequest/view/" + requestId;
        })
        .catch(function (error) {
            //handle error
            console.log('ERROR:');
            console.log(error);
        });
}
