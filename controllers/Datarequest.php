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

	$proposalId = $this->input->get('proposalId');

        $viewParams = array(
            'proposalId'      => $proposalId,
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
            $result = $this->Datarequest_model->submit($arrayPost['formData'], $arrayPost['proposalId']);
	    var_dump($result);die;
        }
    }

    public function data()
    {
        $proposalId = $this->input->get('proposalId');

        $schema = '{
        "description": "Please fill out and submit the form below to submit your data request.",
        "type": "object",
        "properties": {
          "wave": {
            "type": "array",
            "title": "Wave",
            "description": "Please specify the wave(s) from which you would like to obtain data.",
            "items": {
              "type": "string",
              "enum": [
                "Rondom zw - 20 weeks",
                "Rondom zw - 30 weeks",
                "Rondom 0 - 5 mo",
                "Rondom 0 - 10 mo",
                "Rondom 3 (not available yet)",
                "Rondom 6 (not available yet)",
                "Rondom 9",
                "Rondom 12 (not available yet)",
                "Rondom 15 (not available yet)"
              ]
            },
            "uniqueItems": true
          },
          "data": {
            "type": "string",
            "title": "Data",
            "description": "Please specify the data you would like to obtain from the selected wave(s)."
          }
        }
      }';

      $uiSchema = '{
        "wave": {
          "ui:widget": "checkboxes"
        },
        "data": {
          "ui:widget": "textarea"
        }}';

      $output = array();
      $output['schema'] = json_decode($schema);
      $output['uiSchema'] = json_decode($uiSchema);
      $output['proposalId'] = $proposalId;

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
                    $exploded_path = explode('/', $row['DATA_NAME']);
                    $name = str_replace(".json", "", end($exploded_path));
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
