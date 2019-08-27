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
                'js/datarequest/index.js',
            ),
            'items'        => $items,
            'activeModule' => 'datarequest'
        );

        loadView('/datarequest/index', $viewParams);
    }

    public function view($requestId) {

        # Get the data request and data request status from iRODS
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

        # Check if user is a Board of Directors representative. If not, do
        # not allow the user to approve the datarequest
        $rulebody = <<<EORULE
        rule {
            uuGroupUserExists(*group, "*user#*zone", false, *member);
            *member = str(*member);
        }
EORULE;
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            $rulebody,
                array(
                    '*user'  => $this->rodsuser->getUserInfo()['name'],
                    '*zone'  => $this->rodsuser->getUserInfo()['zone'],
                    '*group' => 'datarequests-research-board-of-directors'
                ),
                array('*member')
            );
        $result = $rule->execute()['*member'];
        $isBoardMember = $result == 'true' ? true : false;

        # Check if user is the owner of the datarequest. If so, the approve
        # button will not be rendered

        # Set the default value of $isOwner to true
        $isRequestOwner = true;
        # Get username of datarequest owner
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuIsRequestOwner(*requestId, *currentUserName); }',
            array('*requestId' => $requestId,
                  '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
            array('ruleExecOut')
        );
        $result = json_decode($rule->execute()['ruleExecOut'], true);
        # Get results of isRequestOwner call
        if ($result['status'] == 0) {
            $isRequestOwner = $result['isRequestOwner'];
        }

        # Set view params and render the view
        $viewParams = array(
            'requestId'      => $requestId,
            'request'        => $datarequest,
            'requestStatus'  => $datarequestStatus,
            'isBoardMember'  => $isBoardMember,
            'isRequestOwner' => $isRequestOwner,
            'activeModule'   => 'datarequest',
            'scriptIncludes' => array(
                'js/datarequest/view.js'
            ),
            'styleIncludes'  => array(
                'css/datarequest/view.css'
            )
        );
        loadView('datarequest/datarequest/view', $viewParams);
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
          "type": "object",
          "properties": {
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
                "purpose": {
                  "type": "array",
                  "title": "Purpose",
                  "description": "Data request for the purpose of:",
                  "items": {
                    "type": "string",
                    "enum": [
                      "Analyses in order to publish (e.g. article, report, thesis, etc.)",
                      "Analyses for data quality control only (data will not be published)",
                      "Analyses for descriptive data only, e.g. in order to determine good datasets (data will not be published)"
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
                "wave", "purpose", "data"
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
            "research_proposal": {
              "type": "object",
              "title": "Research proposal",
              "description": "We ask you to provide us with a clear background, methods section and data-analysis plan. These parts of the proposal will be publicly displayed for reference.",
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
            "website": {
              "type": "object",
              "title": "Website",
              "description": "All approved data requests will be made publicly available on our website. Please provide your website summary below.",
              "properties": {
                "project_title": {
                  "type": "string",
                  "title": "Project title"
                },
                "research_question_summary": {
                  "type": "string",
                  "title": "Website summary of research question of your project (max. 200 words)"
                },
                "requested_data_summary": {
                  "type": "string",
                  "title": "Website summary of the data requested for your project",
                  "description": "Please indicate which data you requested to answer your research question"
                }
              },
              "required": [
                "project_title",
                "research_question_summary",
                "requested_data_summary"
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
            "purpose": {
              "ui:widget": "checkboxes",
              "ui:help": "DISCLAIMER DATA ACCESS QUALITY CONTROL AND DESCRIPTIVE DATA: These data can only be used for data quality control analyses or descriptive data analyses only and may not be made public, for example by publishing them or otherwise making them available to others. If you want to use data for disclosure, permission of the YOUth data committee is required, and this data request protocol must be followed for analyses in order to publish."
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

    public function data($requestId) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGetDatarequest(*requestId); }',
            array('*requestId' => $requestId),
            array('ruleExecOut')
        );

        $formData = json_decode($rule->execute()['ruleExecOut'], true)['requestJSON'];

        $this->output->set_content_type('application/json')->set_output($formData);
    }

    public function overview_data()
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
        $data = $this->Datarequest_model->overview($length, $start);

        # Extract summary statistics from data
        $totalItems = $data['summary']['total'];
        $rows = [];

        if ($totalItems > 0) {
            # Parse data
            foreach ($data['rows'] as $row) {
                    $owner      = $row['COLL_OWNER_NAME'];
                    $requestId  = basename($row['COLL_NAME'], '.json');
                    $title      = $row['title'];
                    $requestUri = "<a href='view/" . $requestId . "'>" .
                                  $requestId . "</a>";
                    $name       = $title;
                    $date       = date('Y-m-d H:i:s', $row['COLL_CREATE_TIME']);
                    $status     = $row['META_DATA_ATTR_VALUE'];
                    $rows[]     = array($owner, $requestUri, $name, $date,
                                       $status);
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
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($output));
    }

    public function dmcmembers() {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGroupGetMembersAsJson(*groupName, *members); }',
            array('*groupName' => 'datarequests-research-data-management-committee'),
            array('*members')
        );

        $result = $rule->execute()['*members'];

        $this->output
             ->set_content_type('application/json')
             ->set_output($result);
    }

    public function assignRequest() {
        # Check if user is a data manager
        $rulebody = <<<EORULE
        rule {
            uuGroupUserExists(*group, "*user#*zone", false, *member);
            *member = str(*member);
        }
EORULE;
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            $rulebody,
                array(
                    '*user'  => $this->rodsuser->getUserInfo()['name'],
                    '*zone'  => $this->rodsuser->getUserInfo()['zone'],
                    '*group' => 'datarequests-research-datamanagers'
                ),
                array('*member')
            );
        $result = $rule->execute()['*member'];
        $isDatamanager = $result == 'true' ? true : false;

        if ($isDatamanager) {
            # Get input parameters
            $assignees = $this->input->post()['data'];
            $requestId = $this->input->post()['requestId'];

            # Call uuAssignRequest rule and get status info
            $rule = new ProdsRule(
                $this->rodsuser->getRodsAccount(),
                'rule { uuAssignRequest(*assignees, *requestId); }',
                array('*assignees' => json_encode($assignees), '*requestId' => $requestId),
                array('ruleExecOut')
            );
            $result = $rule->execute()['ruleExecOut'];

            # Return status info
            if (json_decode($result, true)['status'] === 0) {
                $this->output
                ->set_content_type('application/json')
                ->set_output($result);
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output($result);
            }
        }
        else {
            $output['status']     = -2;
            $output['statusInfo'] = "Uploading user is not a datamanager.";

            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(403)
                        ->set_output(json_encode($output));
        }
    }

    public function review($requestId) {
        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest',
            'requestId'        => $requestId
        );

        loadView('/datarequest/review', $viewParams);
    }

    public function reviewSchema()
    {
        $schema = '
        {
          "type": "object",
          "required": [
            "contribution",
            "informed_consent_fit",
            "research_question_answerability",
            "study_quality",
            "logistical_feasibility",
            "study_value",
            "researcher_expertise",
            "biological_samples"
          ],
          "properties": {
            "contribution": {
              "type": "string",
              "title": "How much did the applicant involved contribute to YOUth with respect to recruitment, setup, and continuation of YOUth?"
            },
            "informed_consent_fit": {
              "type": "string",
              "title": "How does the research question fit with the provided informed consent of the participants of YOUth?"
            },
            "research_question_answerability": {
              "type": "string",
              "title": "Can the research question be answered with the requested YOUth data?"
            },
            "study_quality": {
              "type": "string",
              "title": "Is the quality of the proposal good? Is the study design correct?"
            },
            "logistical_feasibility": {
              "type": "string",
              "title": "Is the proposal logistically feasible?"
            },
            "study_value": {
              "type": "string",
              "title": "Is the study valuable?"
            },
            "researcher_expertise": {
              "type": "string",
              "title": "Does the researcher have the expertise necessary to correctly analyze and report on the research question at hand?"
            },
            "biological_samples": {
              "type": "string",
              "title": "Will biological samples be used?",
              "enum": [
                "No",
                "Yes"
              ],
              "default": "No"
            }
          },
          "dependencies": {
            "biological_samples": {
              "oneOf": [
                {
                  "properties": {
                    "biological_samples": {
                      "enum": [
                        "No"
                      ]
                    }
                  }
                },
                {
                  "properties": {
                    "biological_samples": {
                      "enum": [
                        "Yes"
                      ]
                    },
                    "biological_samples_volume": {
                      "type": "string",
                      "title": "Is the volume requested reasonable and does it not seriously deplete the resource?"
                    },
                    "biological_samples_committee_approval": {
                      "type": "string",
                      "title": "Does the committee agree to the use of these samples for the specific research question?"
                    }
                  },
                  "required": [
                    "biological_samples_volume",
                    "biological_samples_committee_approval"
                  ]
                }
              ]
            }
          }
        }';

        $output = array();
        $output['schema'] = json_decode($schema);

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function store_review()
    {
        $arrayPost = $this->input->post();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $rule = new ProdsRule(
                $this->rodsuser->getRodsAccount(),
                'rule { uuSubmitReview(*data, *requestId); }',
                array('*data' => $arrayPost['formData'],
                      '*requestId' => $arrayPost['requestId']),
                array('ruleExecOut')
            );

            $result = json_decode($rule->execute()['ruleExecOut'], true);

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

    public function reviewData($requestId) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGetReview(*requestId); }',
            array('*requestId' => $requestId),
            array('ruleExecOut')
        );

        $formData = json_decode($rule->execute()['ruleExecOut'], true)['reviewJSON'];

        $this->output->set_content_type('application/json')->set_output($formData);
    }

    public function evaluate($requestId) {
        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest',
            'requestId'        => $requestId
        );

        loadView('/datarequest/evaluate', $viewParams);
    }

    public function evaluationSchema()
    {
        $schema = '
        {
          "type": "object",
          "required": [
            "evaluation"
          ],
          "properties": {
            "evaluation": {
              "type": "string",
              "title": "This data request is",
              "enum": [
                "Accepted",
                "Rejected"
              ],
              "default": "Rejected"
            },
            "remarks": {
              "type": "string",
              "title": "Rationale for evaluation"
            }
          }
        }';

        $uiSchema = '
        {
          "remarks": {
            "ui:widget": "textarea"
          }
        }';

        $output = array();
        $output['schema'] = json_decode($schema);
        $output['uiSchema'] = json_decode($uiSchema);

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function store_evaluation()
    {
        # Check if user is a Board of Directors representative. If not, do
        # not allow the user to approve the datarequest
        $rulebody = <<<EORULE
        rule {
            uuGroupUserExists(*group, "*user#*zone", false, *member);
            *member = str(*member);
        }
EORULE;
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            $rulebody,
                array(
                    '*user'  => $this->rodsuser->getUserInfo()['name'],
                    '*zone'  => $this->rodsuser->getUserInfo()['zone'],
                    '*group' => 'datarequests-research-board-of-directors'
                ),
                array('*member')
            );
        $result = $rule->execute()['*member'];
        $isBoardMember = $result == 'true' ? true : false;

        if ($isBoardMember) {
            $arrayPost = $this->input->post();
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                $rule = new ProdsRule(
                    $this->rodsuser->getRodsAccount(),
                    'rule { uuSubmitEvaluation(*data, *requestId); }',
                    array('*data' => $arrayPost['formData'],
                          '*requestId' => $arrayPost['requestId']),
                    array('ruleExecOut')
                );
                $result = json_decode($rule->execute()['ruleExecOut'], true);
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
        } else {
            $output['status']     = -2;
            $output['statusInfo'] = "Uploading user is not a member of the " +
                                    "Board of Directors.";

            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(403)
                        ->set_output(json_encode($output));
        }
    }

    public function upload_dta($requestId) {
        # Check if user is a data manager
        $rulebody = <<<EORULE
        rule {
            uuGroupUserExists(*group, "*user#*zone", false, *member);
            *member = str(*member);
        }
EORULE;
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            $rulebody,
                array(
                    '*user'  => $this->rodsuser->getUserInfo()['name'],
                    '*zone'  => $this->rodsuser->getUserInfo()['zone'],
                    '*group' => 'datarequests-research-datamanagers'
                ),
                array('*member')
            );
        $result = $rule->execute()['*member'];
        $isDatamanager = $result == 'true' ? true : false;

        if ($isDatamanager) {
            # Load Filesystem model
            $this->load->model('filesystem');

            # Replace original filename with "dta.pdf" for easier retrieval
            # later on
            $new_filename = "dta.pdf";
            $_FILES["file"]["name"] = $new_filename;

            # Construct path to data request directory (in which the document will
            # be stored)
            $filePath = '/tempZone/home/datarequests-research/' . $requestId . '/';
            $rodsaccount = $this->rodsuser->getRodsAccount();

            # Upload the document
            $output = $this->filesystem->upload($rodsaccount, $filePath,
                                                $_FILES["file"]);

            # Give the researcher that owns the data request read permissions on
            # the DTA document so he can download it
            $rule = new ProdsRule(
                $this->rodsuser->getRodsAccount(),
                'rule { uuDTAGrantReadPermissions(*requestId, *username); }',
                array('*requestId' => $requestId, '*username' => $this->rodsuser->getUserInfo()['name']),
                array('ruleExecOut')
            );

            $result = json_decode($rule->execute()['ruleExecOut'], true);

            # If upload succeeded, set status to "dta_ready", else return error
            if ($output["status"] == "OK") {
                # Set status to "dta_ready"
                $rule = new ProdsRule(
                    $this->rodsuser->getRodsAccount(),
                    'rule { uuRequestDTAReady(*requestId, *currentUserName); }',
                    array('*requestId' => $requestId,
                          '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
                    array('ruleExecOut')
                );

                $result = json_decode($rule->execute()['ruleExecOut'], true);

                if ($result['status'] == 0) {
                    redirect('/datarequest/view/' + $requestId);
                } else {
                    return $this->output
                                ->set_content_type('application/json')
                                ->set_status_header(500)
                                ->set_output(json_encode($result));
                }
            } else {
                return $this->output
                            ->set_content_type("application/json")
                            ->set_status_header(500)
                            ->set_output(json_encode($output));
            }
        } else {
            $output['status']     = -2;
            $output['statusInfo'] = "Uploading user is not a datamanager.";

            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(403)
                        ->set_output(json_encode($output));
        }
    }

    public function download_dta($requestId)
    {
        # Load Filesystem model
        $this->load->model('filesystem');

        $rodsaccount = $this->rodsuser->getRodsAccount();
        $filePath = '/tempZone/home/datarequests-research/' . $requestId . '/dta.pdf';

        $this->filesystem->download($rodsaccount, $filePath);
    }

    public function upload_signed_dta($requestId) {
        # Check if user is the owner of the datarequest. If so, the approve
        # button will not be rendered

        # Set the default value of $isOwner to true
        $isRequestOwner = true;
        # Get username of datarequest owner
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuIsRequestOwner(*requestId, *currentUserName); }',
            array('*requestId' => $requestId,
                  '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
            array('ruleExecOut')
        );
        $result = json_decode($rule->execute()['ruleExecOut'], true);

        # Get results of isRequestOwner call
        if ($result['status'] == 0) {
            $isRequestOwner = $result['isRequestOwner'];
        }

        if ($isRequestOwner) {
            # Load Filesystem model
            $this->load->model('filesystem');

            # Replace original filename with "signed_dta.pdf" for easier
            # retrieval later on
            $new_filename = "signed_dta.pdf";
            $_FILES["file"]["name"] = $new_filename;

            # Construct path to data request directory (in which the document will
            # be stored)
            $filePath = '/tempZone/home/datarequests-research/' . $requestId . '/';
            $rodsaccount = $this->rodsuser->getRodsAccount();

            # Upload the document
            $output = $this->filesystem->upload($rodsaccount, $filePath,
                                                $_FILES["file"]);

            # Give the data manager read permissions on the signed DTA so he can
            # download it
            $rule = new ProdsRule(
                $this->rodsuser->getRodsAccount(),
                'rule { uuSignedDTAGrantReadPermissions(*requestId, *username); }',
                array('*requestId' => $requestId, '*username' => $this->rodsuser->getUserInfo()['name']),
                array('ruleExecOut')
            );

            $result = json_decode($rule->execute()['ruleExecOut'], true);

            # If upload succeeded, set status to "dta_signed", else return error
            if ($output["status"] == "OK") {
                # Set status to "dta_signed"
                $rule = new ProdsRule(
                    $this->rodsuser->getRodsAccount(),
                    'rule { uuRequestDTASigned(*requestId, *currentUserName); }',
                    array('*requestId' => $requestId,
                          '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
                    array('ruleExecOut')
                );

                $result = json_decode($rule->execute()['ruleExecOut'], true);

                if ($result['status'] == 0) {
                    redirect('/datarequest/view/' . $requestId);
                } else {
                    return $this->output
                                ->set_content_type('application/json')
                                ->set_status_header(500)
                                ->set_output(json_encode($result));
                }
            } else {
                return $this->output
                            ->set_content_type("application/json")
                            ->set_status_header(500)
                            ->set_output(json_encode($output));
            }
        } else {
            $output['status']     = -2;
            $output['statusInfo'] = "Uploading user does not own the data " +
                                    "request.";

            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(403)
                        ->set_output(json_encode($output));
        }
    }

    public function download_signed_dta($requestId)
    {
        # Load Filesystem model
        $this->load->model('filesystem');

        $rodsaccount = $this->rodsuser->getRodsAccount();
        $filePath = '/tempZone/home/datarequests-research/' . $requestId . '/signed_dta.pdf';

        $this->filesystem->download($rodsaccount, $filePath);
    }

    public function data_ready($requestId) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuRequestDataReady(*requestId, *currentUserName); }',
            array('*requestId' => $requestId,
                  '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
            array('ruleExecOut')
        );

        $result = json_decode($rule->execute()['ruleExecOut'], true);

        if ($result['status'] == 0) {
            redirect('/datarequest/view/' . $requestId);
        } else {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(500)
                        ->set_output(json_encode($result));
        }
    }
}
