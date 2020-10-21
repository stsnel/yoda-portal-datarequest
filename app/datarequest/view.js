import React, { Component } from "react";
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import DataSelection, { DataSelectionCart } from "./DataSelection.js";

$(document).ajaxSend((e, request, settings) => {
    // Append a CSRF token to all AJAX POST requests.
    if (settings.type === 'POST' && settings.data.length) {
         settings.data
             += '&' + encodeURIComponent(Yoda.csrf.tokenName)
              + '=' + encodeURIComponent(Yoda.csrf.tokenValue);
    }
});

document.addEventListener("DOMContentLoaded", async () => {

    var datarequestSchema = {};
    var datarequestUiSchema = {};
    var datarequestFormData = {};
    var datarequestStatus = {};

    // Get data request
    Yoda.call('datarequest_get',
        {request_id: requestId},
        {errorPrefix: "Could not get datarequest"})
    .then(datarequest => {
        datarequestFormData = JSON.parse(datarequest.requestJSON);
        datarequestStatus   = datarequest.requestStatus;
    })
    // Set progress bar according to status of data request
    .then(() => {
        let datarequestStatusInt = null;
        switch(datarequestStatus) {
            case 'SUBMITTED':
            case 'PRELIMINARY_ACCEPT':
            case 'PRELIMINARY_REJECT':
            case 'PRELIMINARY_RESUBMIT':
            case 'DATAMANAGER_ACCEPT':
            case 'DATAMANAGER_REJECT':
            case 'DATAMANAGER_RESUBMIT':
                datarequestStatusInt = 0;
                break;
            case 'UNDER_REVIEW':
            case 'REJECTED_AFTER_DATAMANAGER_REVIEW':
            case 'RESUBMIT_AFTER_DATAMANAGER_REVIEW':
                datarequestStatusInt = 1;
                break;
            case 'REVIEWED':
                datarequestStatusInt = 2;
                break;
            case 'APPROVED':
            case 'REJECTED':
            case 'RESUBMIT':
                datarequestStatusInt = 3;
                break;
            case 'DTA_READY':
                datarequestStatusInt = 4;
                break;
            case 'DTA_SIGNED':
                datarequestStatusInt = 5;
                break;
            case 'DATA_READY':
                datarequestStatusInt = 6;
                break;
        }

        // Activate the appropriate steps
        for (const num of Array(datarequestStatusInt + 1).keys()) {
            let elem = document.getElementById("step-" + num);
            elem.classList.remove("disabled");
            elem.classList.add("complete");
        }
    })
    // Get data request schema and uischema
    .then(() => {
        Yoda.call("datarequest_schema_get", {schema_name: "datarequest"})
        .then(response => {
            datarequestSchema   = response.schema;
            datarequestUiSchema = response.uischema;
        })
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
    $("body").on("click", "button.upload_dta", () => {
        $("#uploadDTA").modal("show");
    });

    $("body").on("click", "button.submit_dta", data => {
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
    $("body").on("click", "button.upload_signed_dta", () => {
        $("#uploadSignedDTA").modal("show");
    });

    $("body").on("click", "button.submit_signed_dta", data => {
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
