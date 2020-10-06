import React, { Component } from "react";
import { render } from "react-dom";
import Form from "react-jsonschema-form";
import DataSelection, { DataSelectionCart } from "./DataSelection.js";

document.addEventListener("DOMContentLoaded", async () => {

    var datarequestSchema = {};
    var datarequestUiSchema = {};
    var datarequestFormData = {};

    // Get data request
    Yoda.call('datarequest_get',
        {request_id: requestId},
        {errorPrefix: "Could not get datarequest"})
    .then(datarequest => {
        datarequestFormData = JSON.parse(datarequest.requestJSON);
    })
    // Get data request schema and uiSchema
    .then(async () => {
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

    var prSchema   = {};
    var prUiSchema = {};
    var prFormData = {};

    // Get preliminary review
    Yoda.call('datarequest_preliminary_review_get',
        {request_id: requestId},
        {errorPrefix: "Could not get preliminary review"})
    .then(response => {
        prFormData = JSON.parse(response);
    })
    // Get preliminary review schema and uiSchema
    .then(async () => {
        let response = await fetch("/datarequest/datarequest/preliminaryReviewSchema");

        let schemas = await response.json();

        prSchema   = schemas.schema;
        prUiSchema = schemas.uiSchema;
    })
    // Render preliminary review as disabled form
    .then(() => {
        render(<ContainerReadonly schema={prSchema}
                                  uiSchema={prUiSchema}
                                  formData={prFormData} />,
               document.getElementById("preliminaryReview"));
    });

    var dmrSchema   = {};
    var dmrUiSchema = {};
    var dmrFormData = {};

    // Get data manager review
    Yoda.call("datarequest_datamanager_review_get",
              {request_id: requestId},
              {errorPrefix: "Could not get datamanager review"})
    .then(response => {
        dmrFormData = JSON.parse(response);
    })
    .then(async () => {
        let response = await fetch("/datarequest/datarequest/datamanagerReviewSchema");

        let schemas = await response.json();

        dmrSchema = schemas.schema;
        dmrUiSchema = schemas.uiSchema;
    })
    .then(() => {
        render(<ContainerReadonly schema={dmrSchema}
                                  uiSchema={dmrUiSchema}
                                  formData={dmrFormData} />,
               document.getElementById("datamanagerReview"));
    });

    var assignSchema   = {};
    var assignUiSchema = {};
    var assignFormData = {};

    // Get data manager review
    Yoda.call("datarequest_assignment_get",
              {request_id: requestId},
              {errorPrefix: "Could not get datamanager review"})
    .then(response => {
        assignFormData = JSON.parse(response);
    })
    .then(async () => {
        let response = await fetch("/datarequest/datarequest/assignSchema");

        let schemas = await response.json();

        assignSchema = schemas.schema;
        assignUiSchema = schemas.uiSchema;
    })
    .then(() => {
        render(<ContainerReadonly schema={assignSchema}
                                  uiSchema={assignUiSchema}
                                  formData={assignFormData} />,
               document.getElementById("assign"));
    });

    // Get the schema and uiSchema of the review form
    fetch("/datarequest/datarequest/reviewSchema")
    .then(async response => {
        let schemas = await response.json();

        let reviewSchema = schemas.schema;
        let reviewUiSchema = schemas.uiSchema;

        render(<Container schema={reviewSchema}
                          uiSchema={reviewUiSchema} />,
               document.getElementById("form"));
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

const onSubmit = ({formData}) => submitData(formData);

const CustomDescriptionField = ({id, description}) => {
  return <div id={id} dangerouslySetInnerHTML={{ __html: description }}></div>;
};

const fields = {
  DescriptionField: CustomDescriptionField,
  DataSelection: DataSelectionCart
};

function submitData(data)
{
    // Disable submit button
    $("button:submit").attr("disabled", "disabled");

    // Append username to data
    data.username = username;

    // Submit form and direct to view/
    Yoda.call("datarequest_review_submit",
        {data: data, request_id: requestId},
        {errorPrefix: "Could not submit assignment"})
    .then(() => {
        window.location.href = "/datarequest/view/" + requestId;
    })
    .catch(error => {
        // Re-enable submit button if submission failed
        $("button:submit").attr("disabled", false);
   });
}
