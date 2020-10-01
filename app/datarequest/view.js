import React, { Component } from "react";
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import DataSelection, { DataSelectionCart } from "./DataSelection.js";

$(document).ajaxSend(function(e, request, settings) {
    // Append a CSRF token to all AJAX POST requests.
    if (settings.type === 'POST' && settings.data.length) {
         settings.data
             += '&' + encodeURIComponent(Yoda.csrf.tokenName)
              + '=' + encodeURIComponent(Yoda.csrf.tokenValue);
    }
});

$(document).ready(function() {

    var datarequestSchema = {};
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
        let response = await fetch("/datarequest/datarequest/schema");

        let schemas = await response.json();

        datarequestSchema   = schemas.schema;
        datarequestUiSchema = schemas.uiSchema;
    })
    // Render data request as disabled form
    .then(() => {
        render(<ContainerReadonly schema={datarequestSchema}
                                  uiSchema={datarequestUiSchema}
                                  formData={datarequestFormData} />,
               document.getElementById("datarequest")
        );
    });

    // Render and show the modal for uploading a DTA
    $("body").on("click", "button.upload_dta", function() {
        $("#uploadDTA").modal("show");
    });

    $("body").on("click", "button.submit_dta", function(data) {
        // Prepare form data
        var fd = new FormData(document.getElementById('dta'));
        fd.append(Yoda.csrf.tokenName, Yoda.csrf.tokenValue);

        // Prepare XHR
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/datarequest/datarequest/upload_dta/" + requestId);
        // Reload page after DTA upload
        xhr.onload = location.reload();

        // Send DTA
        xhr.send(fd);
    });

    // Render and show the modal for uploading a signed DTA
    $("body").on("click", "button.upload_signed_dta", function() {
        $("#uploadSignedDTA").modal("show");
    });

    $("body").on("click", "button.submit_signed_dta", function(data) {
        // Prepare form data
        var fd = new FormData(document.getElementById('signed_dta'));
        fd.append(Yoda.csrf.tokenName, Yoda.csrf.tokenValue);

        // Prepare XHR
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/datarequest/datarequest/upload_signed_dta/" + requestId);
        // Reload page after signed DTA upload
        xhr.onload = location.reload();

        // Send signed DTA
        xhr.send(fd);
    });
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
