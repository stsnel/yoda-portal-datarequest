import React, { Component } from "react";
import axios from 'axios';
import { render } from "react-dom";
import Form from "react-jsonschema-form";

var schema = {
  "description": "Please fill out and submit the form below to submit your research proposal.",
  "type": "object",
  "properties": {
    "general": {
      "type": "object",
      "title": "General",
      "properties": {
        "title": {
          "type": "string",
          "title": "Title of the study",
          "description": "One request per article.",
          "maxLength": 2700
        }
      },
      "required": ["title"]
    },
    "contact": {
      "type": "object",
      "title": "Contact person for the proposed study",
      "description": "Please note that this should be level postdoc or higher.",
      "properties": {
        "name": {
          "type": "string",
          "title": "Name"
        },
        "institution": {
          "type": "string",
          "title": "Institution"
        },
        "department": {
          "type": "string",
          "title": "Department"
        },
        "work_address": {
          "type": "string",
          "title": "Address"
        },
        "email": {
          "type": "string",
          "title": "Email"
        },
        "phone": {
          "type": "string",
          "title": "Phone"
        },
        "home_address": {
          "type": "string",
          "title": "Address (personal)"
        }
      },
      "required": ["name", "institution", "department", "work_address", "email", "phone", "home_address"]
    },
    "data_request": {
      "type": "object",
      "title": "Data request",
      "properties": {
        "background": {
          "type": "string",
          "title": "Background of the project",
          "description": "Please provide a short background including the rational of your study as you would do in an introduction of the paper.",
          "maxLength": 500
        },
        "research_question": {
          "type": "string",
          "title": "Research question",
        },
        "methods": {
          "type": "string",
          "title": "Methods",
          "description": "Describe the methods as in the paper in which the data will be presented, according to the categories below, with a total maximum of 1500 words. For a description of task, methods etc. refer to the website, if possible.",
        },
        "design": {
          "type": "string",
          "title": "Design of the study",
          "description": "For instance cross-sectional, longitudinal etc.; substantiate your choices."
        },
        "population": {
          "type": "string",
          "title": "Study population and sample-size",
          "description": "Entire population or a subset; substantiate your choices e.g. Provide a rationale for the requested sample-size, for instance using a power calculation."
        },
        "processing": {
          "type": "string",
          "title": "Data processing and preparation",
          "description": "Including necessary recoding of data etc."
        },
        "missing_data": {
          "type": "string",
          "title": "Handling missing data",
          "description": "Describe how you will detect and handle missingness in the data."
        },
        "analysis_methods": {
          "type": "string",
          "title": "Data analysis methods",
          "description": "Including statistical design and statistical analysis plan. If it is not possible to provide a detailed statistical plan, as this does not fit in with the research questions formulated above, please explain."
        },
        "subgroup_analyses": {
          "type": "string",
          "title": "Planned subgroup analyses",
          "description": "If applicable. Substantiate your choices."
        },
        "sensitivity_analyses": {
          "type": "string",
          "title": "Planned sensitivity analyses",
          "description": "Sensitivity analyses are analyses that you plan beforehand to test whether certain factors have a major influence on your results."
        },
        "timeline": {
          "type": "string",
          "title": "Timeline and milestones",
          "description": "Including dates of when to analyze/write up."
        },
        "output": {
          "type": "string",
          "title": "Output",
          "description": "e.g. article, report, thesis, etc.",
        },
        "proposed_authors": {
          "type": "string",
          "title": "Proposed authors and their affiliations",
          "description": "Please note that the YOUth Data Access Committee can request certain authors to be included.",
        }
      },
      "required": ["background", "research_question", "methods", "design", "population", "processing", "missing_data", "analysis_methods", "subgroup_analyses", "sensitivity_analyses", "timeline", "output", "proposed_authors"]
    },
    "contribution": {
      "type": "object",
      "title": "Contributions to YOUth data collection",
      "description": "The investigator contributes to YOUth with ...",
      "properties": {
        "contribution_time": {
          "type": "string",
          "title": "Time",
          "enum": [
            "No",
            "Yes"
          ],
          "default": "No"
        },
        "contribution_financial": {
          "type": "string",
          "title": "Money",
          "enum": [
            "No",
            "Yes"
          ],
          "default": "No"
        },
        "contribution_favor": {
          "type": "string",
          "title": "Return favor",
          "enum": [
            "No",
            "Yes"
          ],
          "default": "No"
        }
      },
      "required": ["contribution_time", "contribution_financial", "contribution_favor"],
      "dependencies": {
        "contribution_time": {
          "oneOf": [
            {
              "properties": {
                "contribution_time": {
                  "enum": [
                    "No"
                  ]
                }
              }
            },
            {
              "properties": {
                "contribution_time": {
                  "enum": [
                    "Yes"
                  ]
                },
                "contribution_time_type": {
                  "type": "string",
                  "title": "Contribution in time",
                  "enum": [
                    "PhD student", "Other contribution"
                  ]
                },
                "contribution_time_amount": {
                  "type": "number",
                  "title": "Number of hours contribution incl. specification"
                }
              },
              "required": ["contribution_time_type", "contribution_time_amount"]
            }
          ]
        },
        "contribution_financial": {
          "oneOf": [
            {
              "properties": {
                "contribution_financial": {
                  "enum": [
                    "No"
                  ]
                }
              }
            },
            {
              "properties": {
                "contribution_financial": {
                  "enum": [
                    "Yes"
                  ]
                },
                "contribution_financial_amount": {
                  "type": "number",
                  "title": "Financial contribution",
                  "description": "In euros"
                }
              },
              "required": ["contribution_financial_amount"]
            }
          ]
        },
        "contribution_favor": {
          "oneOf": [
            {
              "properties": {
                "contribution_favor": {
                  "enum": [
                    "No"
                  ]
                }
              }
            },
            {
              "properties": {
                "contribution_favor": {
                  "enum": [
                    "Yes"
                  ]
                },
                "contribution_favor_description": {
                  "type": "string",
                  "title": "Detailed description of the return favor."
                }
              },
              "required": ["contribution_favor_description"]
            }
          ]
        }
      }
    }
  }
};

var uiSchema = {
  "title": {
    "ui:autofocus": true
  },
  "data_request": {
    "background": {
      "ui:widget": "textarea"
    },
    "methods": {
      "ui:widget": "textarea"
    },
    "design": {
      "ui:widget": "textarea"
    },
    "population": {
      "ui:widget": "textarea"
    },
    "processing": {
      "ui:widget": "textarea"
    },
    "missing_data": {
      "ui:widget": "textarea"
    },
    "analysis_methods": {
      "ui:widget": "textarea"
    },
    "subgroup_analyses": {
      "ui:widget": "textarea"
    },
    "sensitivity_analyses": {
      "ui:widget": "textarea"
    },
    "timeline": {
      "ui:widget": "textarea"
    },
    "output": {
      "ui:widget": "textarea"
    },
    "proposed_authors": {
      "ui:widget": "textarea"
    }
  },
  "person": {
    "d_contribution_type": {
      "ui:widget": "checkboxes"
    }
  },
  "contribution": {
    "contribution_favor_description": {
      "ui:widget": "textarea"
    }
  }
};

const onSubmit = ({formData}) => submitData(formData);

class YodaForm extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Form className="form"
                  schema={schema}
                  idPrefix={"yoda"}
                  uiSchema={uiSchema}
                  onSubmit={onSubmit}>
                <button ref={(btn) => {this.submitButton=btn;}} className="hidden" />
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
          <YodaForm ref={(form) => {this.form=form;}}/>
          <YodaButtons submitButton={this.submitForm}/>
        </div>
      );
    }
};

render(<Container/>,
    document.getElementById("form")
      );

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

    // Store.
    axios({
        method: 'post',
        url: "store",
        data: bodyFormData,
        config: { headers: {'Content-Type': 'multipart/form-data' }}
        })
        .then(function (response) {
            window.location.href = "/datarequest/researchproposal";
        })
        .catch(function (error) {
            //handle error
            console.log('ERROR:');
            console.log(error);
        });
}
