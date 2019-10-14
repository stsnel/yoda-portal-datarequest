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
        $this->load->model('user');

        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

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
        $isBoardMember = $this->user->isBoardMember();

        # Check if user is a data manager
        $isDatamanager = $this->user->isDatamanager();

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

        # Check if user is assigned to review this proposal
        $isReviewer = false;
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuIsReviewer(*requestId, *currentUserName); }',
            array('*requestId' => $requestId,
                  '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
            array('ruleExecOut')
        );
        $result = json_decode($rule->execute()['ruleExecOut'], true);
        if ($result['status'] == 0) {
            $isReviewer = $result['isReviewer'];
        }

        # Set view params and render the view
        $viewParams = array(
            'tokenName'      => $tokenName,
            'tokenHash'      => $tokenHash,
            'requestId'      => $requestId,
            'request'        => $datarequest,
            'requestStatus'  => $datarequestStatus,
            'isReviewer'     => $isReviewer,
            'isBoardMember'  => $isBoardMember,
            'isDatamanager'  => $isDatamanager,
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

        $this->load->model('Datarequest');

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $result = $this->Datarequest->submit($arrayPost['formData']);

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
            "introduction": {
              "type": "object",
              "title": "Introduction",
              "description": "The information you provide here will be used by the YOUth Data Management Committee to evaluate your data request. Details on this evaluation procedure can be found in the Data Access Protocol. /// Moreover, your data request will be stored in an online repository available to all researchers who submit or have submitted a data request. The aim of this repository is to provide a searchable overview of past, current, and pending data requests. By default, we will publish the following information from your request on our researcher’s website: • After submission of a data request: the names and institutions of the contact person and participating researchers (Section 1) and the research context (Section 2). • After approval of a data request: the complete request (Section 1-5). /// Exception: If you believe that publishing the complete request could do harm (e.g. when you propose to use a novel analysis technique) you can object to publishing the complete request. This should be indicated on the data request form with a rationale (Section 5). The YOUth Data Management Committee will review your matter and advise the YOUth Executive Board whether or not to publish the complete request. If you do not agree with the YOUth Data Management Committee about publishing the complete request, you have the possibility to withdraw your data request."
            },
            "researchers": {
              "type": "object",
              "title": "Researchers",
              "description": "In this section, please provide information about the researchers involved with this data request. • Name, affiliation and contact information of the contact person • Name and details of participating researchers (e.g. intended co-authors) • Name and details of the contact person within YOUth",
              "properties": {
                "contacts": {
                  "type": "array",
                  "title": "Contact person for the proposed study",
                  "description": "Please note that this should be level postdoc or higher.",
                  "minItems": 1,
                  "items": {
                    "type": "object",
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
                      }
                    }
                  },
                  "required": [
                    "name",
                    "institution",
                    "department",
                    "work_address",
                    "email",
                    "phone"
                  ]
                },
                "dmc_contact": {
                  "type": "string",
                  "title": "Contact person in YOUth Data Management Committee",
                  "enum": [
                    "Prof. Dr. M.J.N.L. Benders / Wilhelmina Children\'s Hospital, UMCU / Neonatology / m.benders@umcutrecht.nl",
                    "Prof. Dr. M. Dekovic / Utrecht University / Clinical Child and Family Studies / M.Dekovic@uu.nl",
                    "Prof. Dr. S. Durston / UMCU / Psychiatry / s.durston@umcutrecht.nl",
                    "Prof. Dr. H.E. Hulshoff Pol / UMCU / Psychiatry / h.e.hulshoff@umcutrecht.nl",
                    "Prof. Dr. R.W.J. Kager / Utrecht University / Utrecht Institute of Linguistics OTS / R.W.J.Kager@uu.nl",
                    "Prof. Dr. R. Kahn / Icahn School of Medicine, Mount Sinai, NY / Psychiatry / rkahn@umcutrecht.nl",
                    "Prof. Dr. C. Kemner / Utrecht University / Developmental Psychology / C.Kemner@uu.nl",
                    "Prof. Dr. P.M. Valkenburg / University of Amsterdam / Media, Youth and Society / P.M.Valkenburg@uva.nl",
                    "Prof. Dr. W.A.M. Vollebergh / Utrecht University / Social Sciences / W.A.M.Vollebergh@uu.nl"
                  ],
                  "default": "Prof. Dr. M.J.N.L. Benders / Wilhelmina Children\'s Hospital, UMCU / Neonatology / m.benders@umcutrecht.nl"
                }
              }
            },
            "research_context": {
              "type": "object",
              "title": "Research context",
              "description": "In this section, please briefly describe the context for your research plans. This section should logically introduce the next section (hypotheses). As mentioned, please note that this section will be made publicly available on our researcher’s website after submission of your request. /// Please provide: • The title of your research plan • A very brief background for the topic of your research plan • The rationale for and relevance of your specific research plan • The specific research question(s) or aim(s) of your research (Please also provide a brief specification) • A short description of the data you request /// References can be added at the end of this section (optional).",
              "properties": {
                "title": {
                  "type": "string",
                  "title": "Title of the study",
                  "maxLength": 2700
                },
                "background": {
                  "type": "string",
                  "title": "Background of the topic of your research plan, rationale, relevance (max. 500 words)"
                },
                "research_question": {
                  "type": "string",
                  "title": "The specific research question(s) or aim(s) of your research"
                },
                "requested_data_summary": {
                  "type": "string",
                  "title": "Summary of the data requested for your project",
                  "description": "Please indicate which data you request to answer your research question."
                },
                "references": {
                  "type": "string",
                  "title": "References",
                  "description": "Optional"
                }
              },
              "required": ["title", "background", "research_question", "requested_data_summary"]
            },
            "hypotheses": {
              "type": "object",
              "title": "Hypotheses",
              "description": "In this section, please provide your research hypotheses. For each hypothesis: • Be as specific as possible • Provide the anticipated outcomes for accepting and/or rejecting a hypothesis (or explain why this does not apply to your project, e.g. when using Bayesian statistics) /// Exception: if you plan a hypotheses-free project, please use this section to explain why you don’t formulate specific hypotheses.",
              "properties": {
                "hypotheses": {
                  "type": "string",
                  "title": "Hypotheses"
                }
              }
            },
            "methods": {
              "type": "object",
              "title": "Methods",
              "description": "In this section, you should make clear how the hypotheses are tested. Be as specific as possible. /// Please describe: • The study design and study population (Which data do you require from which subjects?) • The general processing steps (to prepare the data for analysis) • The analysis steps (How are the data analysed to address the hypotheses? If possible, link each description to a specific hypothesis) • Any additional aspects that need to be described to clarify the methodological approach (optional)",
              "properties": {
                "design": {
                  "type": "string",
                  "title": "Study design, study population and sample size",
                  "description": "E.g. cross-sectional or longitudinal; entire population or a subset; substantiate your choices."
                },
                "preparation": {
                  "type": "string",
                  "title": "General processing steps to prepare the data for analysis"
                },
                "processing": {
                  "type": "string",
                  "title": "Specific processing and analysis steps"
                },
                "additional_methodological_aspects": {
                  "type": "string",
                  "title": "Additional methodological aspects",
                  "description": "Optional"
                }
              },
              "required": ["design", "preparation", "processing"]
            },
            "datarequest": {
              "type": "object",
              "title": "Requested data",
              "description": "In this section, please specify as detailed as possible which data (and from which subjects) you request. Include information regarding: • Which wave(s) • Which experiments, questionnaires, etc. • How many sets (sample-size) • Purpose of your data request • Other aspects relevant to your data request (optional).",
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
                  "title": "Additional specifications",
                  "description": "If necessary, please provide additional specifications on the data you would like to obtain (e.g. only of children of which the mother smoked during pregnancy)."
                },
                "experiments_and_number_of_sets": {
                  "type": "string",
                  "title": "Experiments and number of sets you request"
                },
                "other_remarks": {
                  "type": "string",
                  "title": "Other aspects relevant to your data request",
                  "description": "Optional"
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
                "data_lock_notification": {
                  "type": "boolean",
                  "title": "Would you like to be notified when a new data lock is available?",
                  "description": "In principle, data will be made available in data locks twice a year. This means that twice a year, the data is locked on a specific date and that all approved data request projects will receive the same locked data set."
                },
                "publication_approval": {
                  "type": "boolean",
                  "title": "Do you agree with publishing the complete request on our researcher’s website after it is approved (by default)?"
                }
              },
              "required": [
                "wave", "purpose", "experiments_and_number_of_sets", "data_lock_notification", "publication_approval"
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
          "researchers": {
            "contacts": {
              "ui:options": {
                "orderable": false
              }
            }
          },
          "research_context": {
            "background": {
              "ui:widget": "textarea"
            },
            "research_question": {
              "ui:widget": "textarea"
            },
            "requested_data_summary": {
              "ui:widget": "textarea"
            },
            "references": {
              "ui:widget": "textarea"
            }
          },
          "hypotheses": {
            "hypotheses": {
              "ui:widget": "textarea"
            }
          },
          "methods": {
            "design": {
              "ui:widget": "textarea"
            },
            "preparation": {
              "ui:widget": "textarea"
            },
            "processing": {
              "ui:widget": "textarea"
            }
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
            },
            "data_lock_notification": {
              "ui:widget": "radio"
            },
            "publication_approval": {
              "ui:widget": "radio"
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
        $this->load->model('Datarequest');

        # Get configured defaults
        $itemsPerPage = $this->config->item('browser-items-per-page');

        # Get DataTables parameters (for pagination)
        $totalItemsLeftInView = $this->input->get('length');
        $length = $totalItemsLeftInView;
        $start = $this->input->get('start');
        $draw = $this->input->get('draw');

        # Fetch data from iRODS
        $data = $this->Datarequest->overview($length, $start);

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
        $this->load->model('user');

        # Check if user is a data manager
        $isDatamanager = $this->user->isDatamanager();

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
        $this->load->model('user');

        # Check if user is a Board of Directors representative. If not, do
        # not allow the user to approve the datarequest
        $isBoardMember = $this->user->isBoardMember();

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
        $this->load->model('user');

        # Check if user is a data manager
        $isDatamanager = $this->user->isDatamanager();

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
