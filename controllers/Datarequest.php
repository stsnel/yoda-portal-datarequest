<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
                'css/datarequest.css',
                'lib/datatables/css/dataTables.bootstrap.min.css',
                'lib/font-awesome/css/font-awesome.css'
            ),
            'scriptIncludes' => array(
                'lib/datatables/js/jquery.dataTables.min.js',
                'lib/datatables/js/dataTables.bootstrap.min.js',
                'js/datarequest.js',
            ),
            'items' => $items
        );

        loadView('index', $viewParams);
    }

    public function view($rpid) {

	$inputParams = array("*researchProposalId" => $rpid);
	$outputParams = array("*proposalJSON", "*proposalStatus", "*status", "*statusInfo");
	$rule = $this->irodsrule->make("uuGetProposal", $inputParams, $outputParams);

	$proposal = $rule->execute()["*proposalJSON"];
	$proposalStatus = $rule->execute()["*proposalStatus"];

        $viewParams = array(
            'styleIncludes' => array(
                'css/datarequest.css',
            ),
            'rpid' => $rpid,
            'proposal' => $proposal,
	    'proposalStatus' => $proposalStatus
        );

        loadView('view', $viewParams);
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
            'styleIncludes' => array(
                'css/datarequest.css',
            ),
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
        );

        loadView('form', $viewParams);
    }

    public function store()
    {
        $arrayPost = $this->input->post();

        $this->load->model('Proposal_model');

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $result = $this->Proposal_model->submit($arrayPost['formData']);
        }
    }

    public function data()
    {
        $this->load->model('Proposal_model');

	# Get configured defaults
	$itemsPerPage = $this->config->item('browser-items-per-page');

	# Get DataTables parameters (for pagination)
	$totalItemsLeftInView = $this->input->get('length');
	$length = $totalItemsLeftInView;
	$start = $this->input->get('start');
	$draw = $this->input->get('draw');

	# Fetch data from iRODS
	$data = $this->Proposal_model->overview($length, $start);

	# Extract summary statistics from data
	$totalItems = $data['summary']['total'];

	# Parse data
	foreach ($data['rows'] as $row) {
		$owner = $row['COLL_OWNER_NAME'];
		$exploded_path = explode('/', $row['COLL_NAME']);
		$name = end($exploded_path);
		$name = "<a href='view/" . $name . "'>" . $name . "</a>";
		$date = date('Y-m-d H:i:s', $row['COLL_CREATE_TIME']);
		$status = $row['META_DATA_ATTR_VALUE'];
		$rows[] = array($owner, $name, $date, $status);
	}

	# Construct output array for front-end
	$output = array('status' => $data["status"],
			'statusInfo' => $data["statusInfo"],
			'draw' => $draw,
			'recordsTotal' => $totalItems,
			'recordsFiltered' => $totalItems,
			'data' => $rows
	);

	# Return data to DataTables
	$this->output->set_content_type('application/json')->set_output(json_encode($output));
    }
}
