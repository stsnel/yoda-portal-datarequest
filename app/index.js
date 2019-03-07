import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form-uu";

var schema = {
  "description": "Please fill out and submit the form below to submit your research proposal.",
  "type": "object",
  "required": [
    "title",
    "body"
  ],
  "properties": {
    "title": {
      "type": "string",
      "title": "Title"
    },
    "body": {
      "type": "string",
      "title": "Proposal"
    }
  }
};

var uiSchema = {
  "title": {
    "ui:autofocus": true
  },
  "body": {
    "ui:widget": "textarea"
  },
};

const onSubmit = ({formData}) => submitData(formData);

function submitData(data)
{
    var tokenName = form.dataset.csrf_token_name;
    var tokenHash = form.dataset.csrf_token_hash;

    // Create form data.
    var bodyFormData = new FormData();
    bodyFormData.set(tokenName, tokenHash);
    bodyFormData.set('formData', JSON.stringify(data));

    // Store.
    axios({
        method: 'post',
        url: "/datarequest/store",
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

render((
    <Form className="form form-horizontal metadata-form"
          schema={schema}
          uiSchema={uiSchema}
          onSubmit={onSubmit}>
    </Form>
), document.getElementById("form"));
