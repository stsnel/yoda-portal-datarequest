import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form";

$(document).ajaxSend(function(e, request, settings) {
    // Append a CSRF token to all AJAX POST requests.
    if (settings.type === 'POST' && settings.data.length) {
         settings.data
             += '&' + encodeURIComponent(YodaPortal.csrf.tokenName)
              + '=' + encodeURIComponent(YodaPortal.csrf.tokenValue);
    }
});

$(document).ready(function() {

    var datarequestSchema = {};
    var datarequestUiSchema = {};
    var datarequestFormData = {};

    // Get the schema, uiSchema and formData of the data request to be reviewed.
    // Then render the data as a disabled form
    axios.all([
        axios.get("/datarequest/datarequest/schema"),
        axios.get("/datarequest/datarequest/data/" + requestId)
        ])
        .then(axios.spread((schemaresponse, dataresponse) => {
            datarequestFormData = dataresponse.data;
            datarequestSchema   = schemaresponse.data.schema;
            datarequestUiSchema = schemaresponse.data.uiSchema;

            render(<ContainerReadonly schema={datarequestSchema} uiSchema={datarequestUiSchema} formData={datarequestFormData} />,
                document.getElementById("datarequest")
            );
        }));
});

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
