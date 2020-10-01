<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User model
 *
 * @package    Yoda
 * @copyright  Copyright (c) 2019, Utrecht University. All rights reserved.
 * @license    GPLv3, see LICENSE.
 */

class User extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');
    }

    /**
     * Check if user is a Board of Directors representative. If not, do
     * not allow the user to approve the datarequest
     */
    function isBoardMember()
    {
        $result = $this->api->call('group_user_exists',
                      ['group_name' => 'datarequests-research-board-of-directors',
                       'user_name' => $this->rodsuser->getUserInfo()['name'],
                       'include_ro' => false]);

        return $result->data;
    }

    /**
     * Check if user is a Data Management Committee member
     */
    function isDMCMember()
    {
        $result = $this->api->call('group_user_exists',
                      ['group_name' => 'datarequests-research-data-management-committee',
                       'user_name' => $this->rodsuser->getUserInfo()['name'],
                       'include_ro' => false]);

        return $result->data;
    }

    /**
     * Check if user is a data manager
     */
    function isDatamanager()
    {
        $result = $this->api->call('group_user_exists',
                      ['group_name' => 'datarequests-research-datamanagers',
                       'user_name' => $this->rodsuser->getUserInfo()['name'],
                       'include_ro' => false]);

        return $result->data;
    }

    /**
     * Check if user is the owner of a datarequest
     */
    function isRequestOwner($requestId)
    {
        $result = $this->api->call('datarequest_is_owner',
                      ['request_id' => $requestId,
                       'user_name' => $this->rodsuser->getUserInfo()['name']]);

        return $result->data;
    }

    /**
     * Check if user is assigned to review this proposal
     */
    function isReviewer($requestId)
    {
        $result = $this->api->call('datarequest_is_reviewer',
                      ['request_id' => $requestId]);

        return $result->data;
    }
}
