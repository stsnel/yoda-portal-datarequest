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

class Researchproposal extends MY_Controller
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
                'js/researchproposal/index.js',
            ),
            'items'        => $items,
            'activeModule' => 'datarequest'
        );

        loadView('index', $viewParams);
    }

    public function view($rpid) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuGetProposal(*researchProposalId); }',
            array('*researchProposalId' => $rpid),
            array('ruleExecOut')
        );

        $result = json_decode($rule->execute()['ruleExecOut'], true);

        if ($result['status'] != 0) {
            $viewParams = [
                'activeModule' => 'datarequest',
                'rpid' => $rpid
            ];
            loadView('view_permission_denied', $viewParams);
            return;
        }

        $proposal = json_decode($result['proposalJSON'], true);
        $proposalStatus = $result['proposalStatus'];

        # Check if user is a Board of Directors representative. If not, do
        # not allow the user to approve the research proposal
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


        # Check if user is the submitter of the research proposal. If so, the
        # approve button will not be rendered

        # Set the default value of $isOwner to true
        $isProposalOwner = true;

        # Get user ID of proposal owner
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuProposalOwner(*researchProposalId); }',
            array('*researchProposalId' => $rpid),
            array('ruleExecOut')
        );
        $result = json_decode($rule->execute()['ruleExecOut'], true);
        $proposalOwnerUserId = $result['proposalOwnerUserId'];

        # Compare user ID of proposal owner to ID of current user
        if ($result['status'] == 0) {
            $currentUserId = $this->rodsuser->getUserInfo()['id'];
            $isProposalOwner = $currentUserId == $proposalOwnerUserId;
        }


        # Render page

        $this->config->load('config');
        $items = $this->config->item('browser-items-per-page');

        $viewParams = array(
            'rpid'            => $rpid,
            'proposal'        => $proposal,
            'proposalStatus'  => $proposalStatus,
            'isBoardMember'   => $isBoardMember,
            'isProposalOwner' => $isProposalOwner,
            'activeModule'    => 'datarequest',
            'styleIncludes'   => array(
                'lib/datatables/css/dataTables.bootstrap.min.css',
                'lib/font-awesome/css/font-awesome.css'
            ),
            'scriptIncludes'  => array(
                'lib/datatables/js/jquery.dataTables.min.js',
                'lib/datatables/js/dataTables.bootstrap.min.js',
                'js/researchproposal/view.js'
            ),
            'items'           => $items
        );

        loadView('view', $viewParams);
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

    public function assignProposal() {
        # Get input parameters
        $assignees = $this->input->post()['data'];
        $researchProposalId = $this->input->post()['researchProposalId'];

        # Call uuAssignProposal rule and get status info
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuAssignProposal(*assignees, *researchProposalId); }',
            array('*assignees' => json_encode($assignees), '*researchProposalId' => $researchProposalId),
            array('ruleExecOut')
        );
        $result = $rule->execute()['ruleExecOut'];

	# Return status info
        $this->output
             ->set_content_type('application/json')
             ->set_output($result);
    }

    public function approve($rpid) {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuApproveProposal(*researchProposalId); }',
            array('*researchProposalId' => $rpid),
            array('ruleExecOut')
        );        

        $result = json_decode($rule->execute()['ruleExecOut'], true);

        if ($result['status'] == 0) {
            redirect('/datarequest');
        } else {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(500)
                        ->set_output(json_encode($result));
        }
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

        loadView('form', $viewParams);
    }

    public function store()
    {
        $arrayPost = $this->input->post();

        $this->load->model('Proposal_model');

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $result = $this->Proposal_model->submit($arrayPost['formData']);

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
        $rows = [];

        if ($totalItems > 0) {
            # Parse data
            foreach ($data['rows'] as $row) {
                    $owner  = $row['COLL_OWNER_NAME'];
                    $rpid   = basename($row['COLL_NAME'], '.json');
                    $title  = $row['title'];
                    $name   = "<a href='/datarequest/researchproposal/view/" .
                              $rpid . "'>" . $title . "</a>";
                    $date   = date('Y-m-d H:i:s', $row['COLL_CREATE_TIME']);
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
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($output));
    }
}
