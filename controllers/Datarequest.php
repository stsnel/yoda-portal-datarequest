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
                'css/research.css',
                'lib/datatables/css/dataTables.bootstrap.min.css',
                'lib/font-awesome/css/font-awesome.css'
            ),
            'scriptIncludes' => array(
                'lib/datatables/js/jquery.dataTables.min.js',
                'lib/datatables/js/dataTables.bootstrap.min.js',
                'js/datarequest.js',
//                'js/search.js', // may be added later; not needed for MVP
            ),
            'items' => $items,
            'dir' => "DUMMY_DIR"
        );

        loadView('index', $viewParams);
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

	# Get DataTables parameters
	$totalItemsLeftInView = $this->input->get('length');
	$start = $this->input->get('start');
	$draw = $this->input->get('draw');

	# Fetch data from iRODS
	$data = $this->Proposal_model->overview();

	# Extract summary statistics from data
	$totalItems = $data['summary']['total'];

	# Parse data
	foreach ($data['rows'] as $row) {
		$rows[] = array($row['COLL_OWNER_NAME'], $row['COLL_NAME'], date('Y-m-d H:i:s', $row['COLL_CREATE_TIME']));
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
