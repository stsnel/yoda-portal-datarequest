import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import DataSelection, { DataSelectionCart } from "./DataSelection.js";

var datarequestSchema   = {};
var datarequestUiSchema = {};
var datarequestFormData = {};
var prSchema            = {};
var prUiSchema          = {};
var prFormData          = {};
var dmrSchema           = {};
var dmrUiSchema         = {};
var dmrFormData         = {};
var assignSchema        = {};
var assignUiSchema      = {};
var assignFormData      = {};

// Get the schema, uiSchema and formData of the data request to be reviewed.
// Then render the data as a disabled form
axios.all([
    axios.get("/datarequest/datarequest/schema"),
    axios.get("/datarequest/datarequest/data/" + requestId),
    axios.get("/datarequest/datarequest/preliminaryReviewSchema"),
    axios.get("/datarequest/datarequest/preliminaryReviewData/" + requestId),
    axios.get("/datarequest/datarequest/datamanagerReviewSchema"),
    axios.get("/datarequest/datarequest/datamanagerReviewData/" + requestId),
    axios.get("/datarequest/datarequest/assignSchema"),
    axios.get("/datarequest/datarequest/assignData/" + requestId)
    ])
    .then(axios.spread((schemaresponse, dataresponse,
                        prschemaresponse, prdataresponse,
                        dmrschemaresponse, dmrdataresponse,
                        assignschemaresponse, assigndataresponse) => {
        datarequestFormData = dataresponse.data;
        datarequestSchema   = schemaresponse.data.schema;
        datarequestUiSchema = schemaresponse.data.uiSchema;
        prFormData          = prdataresponse.data;
        prSchema            = prschemaresponse.data.schema;
        prUiSchema          = prschemaresponse.data.uiSchema;
        dmrFormData         = dmrdataresponse.data;
        dmrSchema           = dmrschemaresponse.data.schema;
        dmrUiSchema         = dmrschemaresponse.data.uiSchema;
        assignFormData      = assigndataresponse.data;
        assignSchema        = assignschemaresponse.data.schema;
        assignUiSchema      = assignschemaresponse.data.uiSchema;

        render(<ContainerReadonly schema={datarequestSchema}
                                  uiSchema={datarequestUiSchema}
                                  formData={datarequestFormData} />,
            document.getElementById("datarequest")
        );

        render(<ContainerReadonly schema={prSchema}
                                  uiSchema={prUiSchema}
                                  formData={prFormData} />,
            document.getElementById("preliminaryReview")
        );

        render(<ContainerReadonly schema={dmrSchema}
                                  uiSchema={dmrUiSchema}
                                  formData={dmrFormData} />,
            document.getElementById("datamanagerReview")
        );

        render(<ContainerReadonly schema={assignSchema}
                                  uiSchema={assignUiSchema}
                                  formData={assignFormData} />,
            document.getElementById("assign")
        );
    }));

var schema = {};
var uiSchema = {};

// Get the schema and uiSchema of the data request review form
axios.get("/datarequest/datarequest/reviewSchema")
    .then(function (response) {
        console.log(response);
        schema = response.data.schema;
        uiSchema = response.data.uiSchema;

        render(<Container/>,
            document.getElementById("form")
        );
    });

class YodaForm extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Form className="form"
                  schema={schema}
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
        url: "/datarequest/datarequest/store_review",
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
