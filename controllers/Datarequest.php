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
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');
    }

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

    function datarequestStatus($requestId) {
	$result = $this->api->call('datarequest_get', ['request_id' => $requestId]);
	return $result->data->requestStatus;
    }

    public function view($requestId) {
        # Check user group memberships and statuses
        $isBoardMember  = $this->api->call('datarequest_is_bod_member');
        $isDatamanager  = $this->api->call('datarequest_is_datamanager');
        $isDMCMember    = $this->api->call('datarequest_is_dmc_member');
        $isRequestOwner = $this->api->call('datarequest_is_owner', ['request_id' => $requestId]);
        $isReviewer     = $this->api->call('datarequest_is_reviewer', ['request_id' => $requestId]);

        # If the user is neither of the above, return a 403
        if (!$isBoardMember && !$isDatamanager && !$isDMCMember && !$isRequestOwner) {
            $this->output->set_status_header('403');
            return;
        }

        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

	# Get datarequest status
        $requestStatus = $this->datarequestStatus($requestId);

        # Set view params and render the view
        $viewParams = array(
            'tokenName'      => $tokenName,
            'tokenHash'      => $tokenHash,
            'requestId'      => $requestId,
            'requestStatus'  => $requestStatus,
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

    public function add($previousRequestId = NULL) {

        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest'
        );
        if ($previousRequestId) {
            $viewParams['previousRequestId'] = $previousRequestId;
        }

        loadView('/datarequest/add', $viewParams);
    }

    public function preliminaryReview($requestId) {
        // Check if user is board of directors member. If not, return a 403
        $isBoardMember = $this->api->call('datarequest_is_bod_member');
        if (!$isBoardMember) {
            $this->output->set_status_header('403');
            return;
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if ($requestStatus !== "SUBMITTED") {
            $this->output->set_status_header('403');
            return;
        }

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

    public function datamanagerReview($requestId) {
        // Check if user is data manager. If not, return a 403
        $isDatamanager = $this->api->call('datarequest_is_datamanager');

        if (!$isDatamanager) {
            $this->output->set_status_header('403');
            return;
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if (!in_array($requestStatus, ["PRELIMINARY_ACCEPT", "PRELIMINARY_REJECT",
                                      "PRELIMINARY_RESUBMIT"])) {
            $this->output->set_status_header('403');
            return;
        }

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

    public function assign($requestId) {
        // Check if user is board of directors member. If not, return a 403
        $isBoardMember = $this->api->call('datarequest_is_bod_member');
        if (!$isBoardMember) {
            $this->output->set_status_header('403');
            return;
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if (!in_array($requestStatus, ["DATAMANAGER_ACCEPT", "DATAMANAGER_REJECT",
                                       "DATAMANAGER_RESUBMIT"])) {
            $this->output->set_status_header('403');
            return;
        }

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

    public function review($requestId) {
        // Check if user has been assigned as a review to the specified data
        // request. If not, return 403.
        $isReviewer = $this->api->call('datarequest_is_reviewer', ['request_id' => $requestId]);
        if (!$isReviewer) {
            $this->output->set_status_header('403');
            return;
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if ($requestStatus !== "UNDER_REVIEW") {
            $this->output->set_status_header('403');
            return;
        }

        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'username'         => $this->rodsuser->getUserInfo()['name'],
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
            'activeModule'     => 'datarequest',
            'requestId'        => $requestId
        );

        loadView('/datarequest/review', $viewParams);
    }

    public function evaluate($requestId) {
        // Check if user is board of directors member. If not, return a 403
        $isBoardMember = $this->api->call('datarequest_is_bod_member');
        if (!$isBoardMember) {
            $this->output->set_status_header('403');
            return;
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if ($requestStatus !== "REVIEWED") {
            $this->output->set_status_header('403');
            return;
        }

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

    public function upload_dta($requestId) {
        // Check if user is a data manager. If not, return a 403
        $isDatamanager = $this->api->call('datarequest_is_datamanager');
        if (!$isDatamanager) {
            $this->output->set_status_header('403');
            return;
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if ($requestStatus !== "APPROVED") {
            $this->output->set_status_header('403');
            return;
        }

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

        # Perform post-upload actions
        $result = $this->api->call('datarequest_dta_post_upload_actions',
                                  ['request_id' => $requestId]);
    }

    public function download_dta($requestId)
    {
        # Check if user owns the data request. If not, return a 403
        $isRequestOwner = $this->api->call('datarequest_is_owner', ['request_id' => $requestId]);
        if (!$isRequestOwner) {
            $this->output->set_status_header('403');
            return;
        }

        # Load Filesystem model
        $this->load->model('filesystem');

        $rodsaccount = $this->rodsuser->getRodsAccount();
        $filePath = '/tempZone/home/datarequests-research/' . $requestId . '/dta.pdf';

        $this->filesystem->download($rodsaccount, $filePath);
    }

    public function upload_signed_dta($requestId) {
        # Check if user is the owner of the datarequest. If not, return a 403
        $isRequestOwner = $this->api->call('datarequest_is_owner', ['request_id' => $requestId]);
        if (!$isRequestOwner) {
            $this->output->set_status_header('403');
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if ($requestStatus !== "DTA_READY") {
            $this->output->set_status_header('403');
            return;
        }

        # Load Filesystem model
        $this->load->model('filesystem');

        # Replace original filename with "dta_signed.pdf" for easier
        # retrieval later on
        $new_filename = "dta_signed.pdf";
        $_FILES["file"]["name"] = $new_filename;

        # Construct path to data request directory (in which the document will
        # be stored)
        $filePath = '/tempZone/home/datarequests-research/' . $requestId . '/';
        $rodsaccount = $this->rodsuser->getRodsAccount();

        # Upload the document
        $output = $this->filesystem->upload($rodsaccount, $filePath,
                                            $_FILES["file"]);

        # Perform post-upload actions
        $result = $this->api->call('datarequest_signed_dta_post_upload_actions',
                                  ['request_id' => $requestId]);
    }

    public function download_signed_dta($requestId)
    {
        # Check if user is a data manager. If not, return a 403
        $isDatamanager = $this->api->call('datarequest_is_datamanager');
        if (!$isDatamanager) {
            $this->output->set_status_header('403');
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if (!in_array($requestStatus, ["DTA_SIGNED", "DATA_READY"])) {
            $this->output->set_status_header('403');
            return;
        }

        # Load Filesystem model
        $this->load->model('filesystem');

        $rodsaccount = $this->rodsuser->getRodsAccount();
        $filePath = '/tempZone/home/datarequests-research/' . $requestId . '/dta_signed.pdf';

        $this->filesystem->download($rodsaccount, $filePath);
    }

    public function data_ready($requestId) {
        # Check if user is a data manager. If not, return a 403
        $isDatamanager = $this->api->call('datarequest_is_datamanager');
        if (!$isDatamanager) {
            $this->output->set_status_header('403');
            return;
        }

	# Check if request status is appropriate
        $requestStatus = $this->datarequestStatus($requestId);
        if ($requestStatus !== "DTA_SIGNED") {
            $this->output->set_status_header('403');
            return;
        }

        // Set status to data_ready
	$result = $this->api->call('datarequest_data_ready',
                                   ['request_id' => $requestId]);

        // Redirect to view/
        if ($result->status === "ok") {
            redirect('/datarequest/view/' . $requestId);
        }
    }
}
