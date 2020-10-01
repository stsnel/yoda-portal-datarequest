import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import DataSelection, { DataSelectionCart } from "./DataSelection.js";

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

    // Render and show the modal for assigning a data request to one or
    // more DMC members
    $("body").on("click", "button.assign", function() {
        // Get list of DMC members
        $.getJSON("/datarequest/datarequest/dmcmembers", function (data) {
            if (data.length > 0) {
                // Wipe the selection in case the user previously made a
                // selection
                $("#dmc-members-list").html("");

                // Construct the multiselect list of options (i.e. DMC members)
                for (let member of data) {
                    $("#dmc-members-list").append(new Option(member));
                }
            } else {
                setMessage("error", "No DMC members configured.");
            }
        });

        // Show the modal
        $("#assignForReview").modal("show");
    });

    // Assign a data request to one or more DMC members
    $("body").on("click", "button.submit-assignment", function(data) {
        // Get selected assignees
        let assignees = $("#dmc-members-list").val();

        // Submit assignees to controller (which will call the appropriate
        // iRODS rule)
        $.post("/datarequest/datarequest/assignRequest",
               {"data": assignees, "requestId": requestId},
               function (data) {
                // Reload the current page so that the status field is
                // updated
                location.reload();
        });
    });

    // Render and show the modal for uploading a DTA
    $("body").on("click", "button.upload_dta", function() {
        $("#uploadDTA").modal("show");
    });

    $("body").on("click", "button.submit_dta", function(data) {
        // Upload data
        var xhr = new XMLHttpRequest();
        var fd = new FormData(document.getElementById('dta'));

        fd.append(YodaPortal.csrf.tokenName, YodaPortal.csrf.tokenValue);

        xhr.open("POST", "/datarequest/datarequest/upload_dta/" + requestId);
        xhr.onload = location.reload();

        xhr.send(fd);
    });

    // Render and show the modal for uploading a signed DTA
    $("body").on("click", "button.upload_signed_dta", function() {
        $("#uploadSignedDTA").modal("show");
    });

    $("body").on("click", "button.submit_signed_dta", function(data) {
        // Upload data
        var xhr = new XMLHttpRequest();
        var fd = new FormData(document.getElementById('signed_dta'));

        fd.append(YodaPortal.csrf.tokenName, YodaPortal.csrf.tokenValue);

        xhr.open("POST", "/datarequest/datarequest/upload_signed_dta/" + requestId);
        xhr.onload = location.reload();

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
