<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Datarequest controller
 *
 * @package    Yoda
 * @copyright  Copyright (c) 2019, Utrecht University. All rights reserved.
 * @license    GPLv3, see LICENSE.
 */
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;

class Datarequest extends MY_Controller
{
    public function index() {
        $this->config->load('config');
        $items = $this->config->item('browser-items-per-page');

        $viewParams = array(
            'styleIncludes' => array(
                'lib/datatables/css/dataTables.bootstrap.min.css',
                'lib/font-awesome/css/font-awesome.css'
            ),
            'scriptIncludes' => array(
                'lib/datatables/js/jquery.dataTables.min.js',
                'lib/datatables/js/dataTables.bootstrap.min.js',
                'js/datarequest.js',
            ),
            'items'        => $items,
            'activeModule' => 'datarequest'
        );

        loadView('index', $viewParams);
    }

    public function view($requestId) {

        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGetDatarequest(*requestId); }',
            array('*requestId' => $requestId),
            array('ruleExecOut')
        );

        $result = json_decode($rule->execute()['ruleExecOut'], true);

        if ($result['status'] != 0) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_status_header(500)
                 ->set_output(json_encode($result));
        }

        $datarequest = json_decode($result["requestJSON"], true);
        $datarequestStatus = $result["requestStatus"];
        $proposalId = $result['proposalId'];

        $viewParams = array(
            'requestId'     => $requestId,
            'request'       => $datarequest,
            'requestStatus' => $datarequestStatus,
            'proposalId'    => $proposalId,
            'activeModule'  => 'datarequest'
        );

        loadView('datarequest/datarequest/view', $viewParams);
    }

    public function approve($rpid) {
        $inputParams = array("*researchProposalId" => $rpid);
        $outputParams = array("*status", "*statusInfo");
        $rule = $this->irodsrule->make("uuApproveProposal", $inputParams, $outputParams);

        $results = $rule->execute();

        redirect('/datarequest');
    }

    public function add() {

        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest'
        );

        loadView('/datarequest/add', $viewParams);
    }

    public function store()
    {
        $arrayPost = $this->input->post();

        $this->load->model('Datarequest_model');

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $result = $this->Datarequest_model->submit($arrayPost['formData']);

            if ($result['status'] == 0) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_output(json_encode($result));
            } else {
                $this->output
                     ->set_content_type('application/json')
                     ->set_status_header(500)
                     ->set_output(json_encode($result));
            }
        }
    }

    public function schema()
    {
        $schema = '{
          "description": "Please complete and submit this form to register your data request.",
          "type": "object",
          "properties": {
            "datarequest": {
              "type": "object",
              "title": "Requested data",
              "properties": {
                "wave": {
                  "type": "array",
                  "title": "Wave",
                  "description": "Please specify the wave(s) from which you would like to obtain data.",
                  "items": {
                    "type": "string",
                    "enum": [
                      "Around pregnancy - 20 weeks",
                      "Around pregrancy - 30 weeks",
                      "Around 0 - 5 mo",
                      "Around 0 - 10 mo",
                      "Around 3 (not available yet)",
                      "Around 6 (not available yet)",
                      "Around 9",
                      "Around 12 (not available yet)",
                      "Around 15 (not available yet)"
                    ]
                  },
                  "minItems": 1,
                  "uniqueItems": true
                },
                "data": {
                  "type": "string",
                  "title": "Data",
                  "description": "Please specify the data you would like to obtain from the selected wave(s)."
                }
              },
              "required": [
                "wave", "data"
              ]
            },
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
              "required": [
                "title"
              ]
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
              "required": [
                "name",
                "institution",
                "department",
                "work_address",
                "email",
                "phone",
                "home_address"
              ]
            },
            "research_proposal": {
              "type": "object",
              "title": "Data request",
              "properties": {
                "background": {
                  "type": "string",
                  "title": "Background of the project",
                  "description": "Please provide a short background including the rational of your study as you would do in an introduction of the paper."
                },
                "research_question": {
                  "type": "string",
                  "title": "Research question"
                },
                "methods": {
                  "type": "string",
                  "title": "Methods",
                  "description": "Describe the methods as in the paper in which the data will be presented, according to the categories below, with a total maximum of 1500 words. For a description of task, methods etc. refer to the website, if possible."
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
                  "description": "e.g. article, report, thesis, etc."
                },
                "proposed_authors": {
                  "type": "string",
                  "title": "Proposed authors and their affiliations",
                  "description": "Please note that the YOUth Data Access Committee can request certain authors to be included."
                }
              },
              "required": [
                "background",
                "research_question",
                "methods",
                "design",
                "population",
                "processing",
                "missing_data",
                "analysis_methods",
                "subgroup_analyses",
                "sensitivity_analyses",
                "timeline",
                "output",
                "proposed_authors"
              ]
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
              "required": [
                "contribution_time",
                "contribution_financial",
                "contribution_favor"
              ],
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
                            "PhD student",
                            "Other contribution"
                          ]
                        },
                        "contribution_time_amount": {
                          "type": "number",
                          "title": "Number of hours contribution incl. specification"
                        }
                      },
                      "required": [
                        "contribution_time_type",
                        "contribution_time_amount"
                      ]
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
                      "required": [
                        "contribution_financial_amount"
                      ]
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
                      "required": [
                        "contribution_favor_description"
                      ]
                    }
                  ]
                }
              }
            }
          }
        }';

        $uiSchema = '{
          "title": {
            "ui:autofocus": true
          },
          "datarequest": {
            "wave": {
              "ui:widget": "checkboxes"
            },
            "data": {
              "ui:widget": "textarea"
            }
          },
          "research_proposal": {
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
        }';

        $output = array();
        $output['schema'] = json_decode($schema);
        $output['uiSchema'] = json_decode($uiSchema);

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function overview($proposalId)
    {
        $this->load->model('Datarequest_model');

        # Get configured defaults
        $itemsPerPage = $this->config->item('browser-items-per-page');

        # Get DataTables parameters (for pagination)
        $totalItemsLeftInView = $this->input->get('length');
        $length = $totalItemsLeftInView;
        $start = $this->input->get('start');
        $draw = $this->input->get('draw');

        # Fetch data from iRODS
        $data = $this->Datarequest_model->overview($proposalId, $length, $start);

        # Extract summary statistics from data
        $totalItems = $data['summary']['total'];
        $rows = [];

        if ($totalItems > 0) {
            # Parse data
            foreach ($data['rows'] as $row) {
                    $owner = $row['DATA_OWNER_NAME'];
                    $name = basename($row['DATA_NAME'], ".json");
                    $name = "<a href='/datarequest/datarequest/view/" . $name . "'>" . $name . "</a>";
                    $date = date('Y-m-d H:i:s', $row['DATA_CREATE_TIME']);
                    $status = $row['META_DATA_ATTR_VALUE'];
                    $rows[] = array($owner, $name, $date, $status);
            }
        }

        # Construct output array for front-end
        $output = array('status'          => $data["status"],
                        'statusInfo'      => $data["statusInfo"],
                        'draw'            => $draw,
                        'recordsTotal'    => $totalItems,
                        'recordsFiltered' => $totalItems,
                        'data'            => $rows
        );

        # Return data to DataTables
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }
}
