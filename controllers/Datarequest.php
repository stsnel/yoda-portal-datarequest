<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Datarequest controller
 *
 * @package    Yoda
 * @copyright  Copyright (c) 2019, Utrecht University. All rights reserved.
 * @license    GPLv3, see LICENSE.
 */

class Datarequest extends MY_Controller
{
    public function index() {
        $this->config->load('config');
        $items = $this->config->item('browser-items-per-page');

        $viewParams = array(
            'styleIncludes' => array(
                'lib/datatables/css/dataTables.bootstrap.min.css',
                'lib/font-awesome/css/font-awesome.css',
                'css/datarequest/index.css'
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
        $isRequestOwner = $this->user->isRequestOwner($requestId);

        # Check if user is assigned to review this proposal.
        $isReviewer = $this->user->isReviewer($requestId);

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
            "introduction": {
              "type": "object",
              "title": "Introduction",
              "description": "The information you provide here will be used by the YOUth Executive Board, the Data Manager, and the Data Management Committee to evaluate your data request. Details regarding this evaluation procedure can be found in the Data Access Protocol.<br/><br/>All data requests will be published on the YOUth researcher’s website in order to provide a searchable overview of past, current, and pending data requests. By default, the publication of submitted and pending data requests includes he names and institutions of the contact person and participating researchers as well as a broad description of the research context.</br></br>After approval of a data request, the complete request (including hypotheses and proposed analyses) will be published. If an applicant has reasons to object to the publication of their complete data request, they should notify the Project Manager, who will evaluate the objection   with the other members of the Executive Board and the Data Management Committee. If the objection is rejected, the researcher may decide to withdraw their data request."
            },
            "researchers": {
              "type": "object",
              "title": "Researchers",
              "description": "In this section, please provide information about the researchers involved with this data request.</br><ul><li>Name, affiliation and contact information of the contact person</li><li>Name and details of participating researchers (e.g. intended co-authors)</li><li>Name and details of the contact person within YOUth (if any)</li></ul>",
              "properties": {
                "contacts": {
                  "type": "array",
                  "title": "Contact person(s) for the proposed study",
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
                  "title": "Contact person in YOUth Data Management Committee (if any)",
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
                  ]
                }
              }
            },
            "research_context": {
              "type": "object",
              "title": "Research context",
              "description": "In this section, please briefly describe the context for your research plans. This section should logically introduce the next section (hypotheses). As mentioned, please note that this section will be made publicly available on our researcher’s website after submission of your request.<br/>Please provide:<br/><ul><li>The title of your research plan</li><li>A very brief background for the topic of your research plan</li><li>The rationale for and relevance of your specific research plan</li><li>The specific research question(s) or aim(s) of your research (Please also provide a brief specification)</li><li>A short description of the data you request</li></ul>References can be added at the end of this section (optional).",
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
              "description": "In this section, please provide your research hypotheses. For each hypothesis:<ul><li>Be as specific as possible</li><li>Provide the anticipated outcomes for accepting and/or rejecting a hypothesis</li></ul>",
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
              "description": "In this section, you should make clear how the hypotheses are tested. Be as specific as possible.<br/>Please describe:<ul><li>The study design and study population (Which data do you require from which subjects?)</li><li>The general processing steps (to prepare the data for analysis)</li>The analysis steps (How are the data analysed to address the hypotheses? If possible, link each description to a specific hypothesis)</li><li>Any additional aspects that need to be described to clarify the methodological approach (optional)</li></ul>",
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
                  "title": "Specific processing and analysis steps to address the hypotheses"
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
              "description": "In this section, please specify as detailed as possible which data you request (and from which subjects). Include information regarding:<ul><li>Which wave(s)</li><li>Which experiments, questionnaires, etc.</li><li>How many sets (sample-size)</li><li>Purpose of your data request</li><li>Other aspects relevant to your data request (optional).</li></ul>Some information on the use of the data selection table below:<ul><li>Use the sorting options to find the data set(s) you would like to request</li><li>Click the checkbox or the row to add the data to your \"shopping cart\"</li><li>Click the (+) to show additional information about a data set (if available)</li><li>Your selection is shown in the shopping cart below the data selection table</li><li>You may change the number of rows per page using the bottom-left drop-down menu</li></ul>",
              "properties": {
                "data": {
                  "type": "object",
                  "title": "Data"
                },
                "additional_specifications": {
                  "type": "string",
                  "title": "Additional specifications",
                  "description": "If necessary, please provide additional specifications on the data you would like to obtain (e.g. only of children of which the mother smoked during pregnancy)."
                },
                "other_remarks": {
                  "type": "string",
                  "title": "Other aspects relevant to your data request",
                  "description": "Optional"
                },
                "purpose": {
                  "type": "string",
                  "title": "Purpose",
                  "description": "Data request for the purpose of:",
                  "enum": [
                    "Analyses in order to publish",
                    "Analyses for data assessment only (results will not be published)"
                  ]
                },
                "data_lock_notification": {
                  "type": "boolean",
                  "title": "Would you like to be notified when a new data lock is available?",
                  "enumNames": [ "Yes", "No" ]
                },
                "publication_approval": {
                  "type": "boolean",
                  "title": "Do you agree with publishing the complete request on our researcher’s website after it is approved?",
                  "enumNames": [ "Yes", "No (please provide a rationale)" ]
                }
              },
              "dependencies": {
                "purpose": {
                  "oneOf": [
                    {
                      "properties": {
                        "purpose": {
                          "enum": [
                            "Analyses for data assessment only (results will not be published)"
                          ]
                        }
                      }
                    },
                    {
                      "properties": {
                        "purpose": {
                          "enum": [
                            "Analyses in order to publish"
                          ]
                        },
                        "publication_type": {
                          "type": "string",
                          "title": "Publication type",
                          "enum": [
                            "Article or report",
                            "PhD thesis"
                          ]
                        }
                      },
                      "required": [
                        "publication_type"
                      ]
                    }
                  ]
                },
                "publication_approval": {
                  "oneOf": [
                    {
                      "properties": {
                        "publication_approval": {
                          "enum": [
                            true
                          ]
                        }
                      }
                    },
                    {
                      "properties": {
                        "publication_approval": {
                          "enum": [
                            false
                          ]
                        },
                        "private_request_explanation": {
                          "type": "string",
                          "title": "Please explain why your request should not be made public."
                        }
                      },
                      "required": [
                        "private_request_explanation"
                      ]
                    }
                  ]
                }
              },
              "required": [
                "data", "purpose", "data_lock_notification", "publication_approval"
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
                        "contribution_time_specification": {
                          "type": "string",
                          "title": "Number of hours of contribution incl. specification"
                        }
                      },
                      "required": [
                        "contribution_time_type",
                        "contribution_time_specification"
                      ]
                    }
                  ]
                },
                "contribution_time_type": {
                  "oneOf": [
                    {
                      "properties": {
                        "contribution_time_type": {
                          "enum": [
                            "PhD student"
                          ]
                        }
                      }
                    },
                    {
                      "properties": {
                        "contribution_time_type": {
                          "enum": [
                            "Other contribution"
                          ]
                        },
                        "contribution_time_type_other": {
                          "type": "string",
                          "title": "Who will provide the time contribution?"
                        }
                      },
                      "required": [
                        "contribution_time_type_other"
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
                          "description": "In euros",
                          "minimum": 0
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
            },
            "additional_methodological_aspects": {
              "ui:widget": "textarea"
            }
          },
          "datarequest": {
            "ui:order": [ "data", "additional_specifications", "other_remarks", "purpose", "publication_type", "data_lock_notification", "publication_approval", "private_request_explanation" ],
            "data": {
              "ui:field": "DataSelection"
            },
            "additional_specifications": {
              "ui:widget": "textarea"
            },
            "purpose": {
              "ui:widget": "radio"
            },
            "publication_type": {
              "ui:widget": "radio"
            },
            "other_remarks": {
              "ui:widget": "textarea"
            },
            "data_lock_notification": {
              "ui:widget": "radio"
            },
            "publication_approval": {
              "ui:widget": "radio"
            },
            "private_request_explanation": {
              "ui:widget": "textarea"
            }
          },
          "person": {
            "d_contribution_type": {
              "ui:widget": "checkboxes"
            }
          },
          "contribution": {
            "ui:order": [ "contribution_time", "contribution_time_type", "contribution_time_type_other", "contribution_time_specification", "contribution_financial", "contribution_financial_amount", "contribution_favor", "contribution_favor_description" ],
            "contribution_time_specification": {
              "ui:widget": "textarea"
            },
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

    public function preliminaryReview($requestId) {
        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest',
            'requestId'        => $requestId
        );

        loadView('/datarequest/preliminaryreview', $viewParams);
    }

    public function storePreliminaryReview()
    {
        $arrayPost = $this->input->post();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $rule = new ProdsRule(
                $this->rodsuser->getRodsAccount(),
                'rule { uuSubmitPreliminaryReview(*data, *requestId); }',
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

    public function preliminaryReviewSchema()
    {
        $schema = '
        {
          "type": "object",
          "title": "Preliminary review",
          "properties": {
            "preliminary_review": {
              "type": "string",
              "title": "This data request is",
              "enum": [
                "Accepted for data manager review",
                "Rejected"
              ]
            },
            "internal_remarks": {
              "type": "string",
              "title": "Internal remarks",
              "description": "Any remarks about the data request go here. In case of rejection, please provide a rationale here. The researcher cannot read these remarks."
            }
          },
          "dependencies": {
            "preliminary_review": {
              "oneOf": [
                {
                  "properties": {
                    "preliminary_review": {
                      "enum": [
                        "Accepted for data manager review"
                      ]
                    }
                  }
                },
                {
                  "properties": {
                    "preliminary_review": {
                      "enum": [
                        "Rejected"
                      ]
                    },
                    "internal_remarks": {
                      "type": "string",
                      "title": "Internal remarks",
                      "description": "Any remarks about the data request go here. In case of rejection, please provide a rationale here. The researcher cannot read these remarks."
                    },
                    "feedback_for_researcher": {
                      "type": "string",
                      "title": "Feedback for researcher",
                      "description": "Please provide feedback to the researcher in case of rejection here. This feedback will be included in the rejection email."
                    }
                  },
                  "required": [
                    "internal_remarks", "feedback_for_researcher"
                  ]
                }
              ]
            }
          },
          "required": [
            "preliminary_review"
          ]
        }';

        $uiSchema = '
        {
          "internal_remarks": {
            "ui:widget": "textarea"
          },
          "feedback_for_researcher": {
            "ui:widget": "textarea"
          }
        }';

        $output = array();
        $output['schema'] = json_decode($schema);
        $output['uiSchema'] = json_decode($uiSchema);

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function preliminaryReviewData($requestId) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGetPreliminaryReview(*requestId); }',
            array('*requestId' => $requestId),
            array('ruleExecOut')
        );

        $formData = json_decode($rule->execute()['ruleExecOut'], true)['preliminaryReviewJSON'];

        $this->output->set_content_type('application/json')->set_output($formData);
    }

    public function datamanagerReview($requestId) {
        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest',
            'requestId'        => $requestId
        );

        loadView('/datarequest/datamanagerreview', $viewParams);
    }

    public function storeDatamanagerReview()
    {
        $arrayPost = $this->input->post();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $rule = new ProdsRule(
                $this->rodsuser->getRodsAccount(),
                'rule { uuSubmitDatamanagerReview(*data, *requestId); }',
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

    public function datamanagerReviewSchema()
    {
        $schema = '
        {
          "type": "object",
          "title": "Data manager review",
          "properties": {
            "datamanager_review": {
              "type": "string",
              "title": "I advise that this data request be",
              "enum": [
                "Accepted",
                "Rejected"
              ]
            },
            "datamanager_remarks": {
              "type": "string",
              "title": "Data manager remarks",
              "description": "Any advisory remarks about the data request go here. In case of rejection, an explanation is mandatory. The researcher cannot read these remarks."
            }
          },
          "dependencies": {
            "datamanager_review": {
              "oneOf": [
                {
                  "properties": {
                    "datamanager_review": {
                      "enum": [
                        "Accepted"
                      ]
                    }
                  }
                },
                {
                  "properties": {
                    "datamanager_review": {
                      "enum": [
                        "Rejected"
                      ]
                    },
                    "datamanager_remarks": {
                      "type": "string",
                      "title": "Data manager remarks",
                      "description": "Any advisory remarks about the data request go here. In case of rejection, an explanation is mandatory. The researcher cannot read these remarks."
                    }
                  },
                  "required": [
                    "datamanager_remarks"
                  ]
                }
              ]
            }
          },
          "required": [
            "datamanager_review"
          ]
        }';

        $uiSchema = '
        {
          "datamanager_remarks": {
            "ui:widget": "textarea"
          }
        }';

        $output = array();
        $output['schema'] = json_decode($schema);
        $output['uiSchema'] = json_decode($uiSchema);

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function datamanagerReviewData($requestId) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGetDatamanagerReview(*requestId); }',
            array('*requestId' => $requestId),
            array('ruleExecOut')
        );

        $formData = json_decode($rule->execute()['ruleExecOut'], true)['datamanagerReviewJSON'];

        $this->output->set_content_type('application/json')->set_output($formData);
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

    public function assign($requestId) {
        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest',
            'requestId'        => $requestId
        );

        loadView('/datarequest/assign', $viewParams);
    }

    public function storeAssign()
    {
        $arrayPost = $this->input->post();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $rule = new ProdsRule(
                $this->rodsuser->getRodsAccount(),
                'rule { uuSubmitAssignment(*data, *requestId); }',
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

    public function assignSchema()
    {
        $schema = '
        {
          "type": "object",
          "title": "Assignment",
          "description": "Please consider carefully the remarks of the data manager, if any.",
          "properties": {
            "decision": {
              "type": "string",
              "title": "This data request is:",
              "enum": [
                "Accepted for DMC review",
                "Rejected"
              ]
            },
            "response_to_dm_remarks": {
              "type": "string",
              "title": "Response to data manager remarks (if any)"
            }
          },
          "dependencies": {
            "decision": {
              "oneOf": [
                {
                  "properties": {
                    "decision": {
                      "enum": [
                        "Accepted for DMC review"
                      ]
                    },
                    "assign_to": {
                      "type": "array",
                      "title": "Please select the DMC member(s) to whom the data request should be assigned for review.",
                      "items": {
                        "type": "string",
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
                        ]
                      },
                      "uniqueItems": true
                    }
                  },
                  "required": [
                    "assign_to"
                  ]
                },
                {
                  "properties": {
                    "decision": {
                      "enum": [
                        "Rejected"
                      ]
                    },
                    "feedback_for_researcher": {
                      "type": "string",
                      "title": "Feedback for researcher",
                      "description": "Please provide feedback to the researcher in case of rejection here. This feedback will be included in the rejection email."
                    }
                  },
                  "required": [
                    "feedback_for_researcher"
                  ]
                }
              ]
            }
          },
          "required": [
            "decision"
          ]
        }';

        $uiSchema = '
        {
          "response_to_dm_remarks": {
            "ui:widget": "textarea"
          },
          "assign_to": {
            "ui:widget": "checkboxes"
          },
          "feedback_for_researcher": {
            "ui:widget": "textarea"
          }
        }';

        $output = array();
        $output['schema'] = json_decode($schema);
        $output['uiSchema'] = json_decode($uiSchema);

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
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

    public function assignData($requestId) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGetAssignment(*requestId); }',
            array('*requestId' => $requestId),
            array('ruleExecOut')
        );

        $formData = json_decode($rule->execute()['ruleExecOut'], true)['assignmentJSON'];

        $this->output->set_content_type('application/json')->set_output($formData);
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
            "evaluation",
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
            "evaluation": {
              "type": "string",
              "title": "Would you approve / reject / reject (resubmit) this data request?",
              "enum": [
                "Approve",
                "Reject",
                "Reject (resubmit)"
              ]
            },
            "evaluation_rationale": {
              "type": "string",
              "title": "Please provide a brief rationale for your evaluation.",
              "description": "This is mandatory if the data request is rejected."
            },
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
            "evaluation": {
              "oneOf": [
                {
                  "properties": {
                    "evaluation": {
                      "enum": [
                        "Approve"
                      ]
                    }
                  }
                },
                {
                  "properties": {
                    "evaluation": {
                      "enum": [
                        "Reject",
                        "Reject (resubmit)"
                      ]
                    }
                  },
                  "required": [
                    "evaluation_rationale"
                  ]
                }
              ]
            },
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
                "Approved",
                "Rejected"
              ]
            },
            "internal_remarks": {
              "type": "string",
              "title": "Internal remarks, if any. Mandatory if data request is rejected. The researcher cannot read these remarks."
            }
          },
          "dependencies": {
            "evaluation": {
              "oneOf": [
                {
                  "properties": {
                    "evaluation": {
                      "enum": [
                        "Approved"
                      ]
                    }                
                  }
                },
                {
                  "properties": {
                    "evaluation": {
                      "enum": [
                        "Rejected"
                      ]
                    },
                    "feedback_for_researcher": {
                      "type": "string",
                      "title": "Feedback for researcher",
                      "description": "Please provide feedback to the researcher in case of rejection here. This feedback will be included in the rejection email."
                    }
                  },
                  "required": [
                    "internal_remarks", "feedback_for_researcher"
                  ]
                }
              ]
            }
          }
        }';

        $uiSchema = '
        {
          "internal_remarks": {
            "ui:widget": "textarea"
          },
          "feedback_for_researcher": {
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
        $this->load->model('user');

        # Check if user is the owner of the datarequest. If so, the approve
        # button will not be rendered
        $isRequestOwner = $this->user->isRequestOwner($requestId);

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
