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
    /**
     * Check if user is a Board of Directors representative. If not, do
     * not allow the user to approve the datarequest
     */
    function isBoardMember()
    {
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

        return $result == 'true' ? true : false;
    }

    /**
     * Check if user is a Data Management Committee member.
     */
    function isDMCMember()
    {
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
                    '*group' => 'datarequests-research-data-management-committee'
                ),
                array('*member')
            );
        $result = $rule->execute()['*member'];

        return $result == 'true' ? true : false;
    }

    /**
     * Check if user is a data manager.
     */
    function isDatamanager()
    {
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

        return $result == 'true' ? true : false;
    }

    /**
     * Check if user is the owner of a datarequest.
     */
    function isRequestOwner($requestId)
    {
        $isRequestOwner = false;

        # Get username of datarequest owner.
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuIsRequestOwner(*requestId, *currentUserName); }',
            array('*requestId' => $requestId,
                  '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
            array('ruleExecOut')
        );
        $result = json_decode($rule->execute()['ruleExecOut'], true);

        # Get results of uuIsRequestOwner call.
        if ($result['status'] == 0) {
            $isRequestOwner = $result['isRequestOwner'];
        }

        return $isRequestOwner;
    }

    /**
     * Check if user is assigned to review this proposal.
     */
    function isReviewer($requestId)
    {
        $isReviewer = false;

        # Check if user is assigned to review this proposal.
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuIsReviewer(*requestId, *currentUserName); }',
            array('*requestId' => $requestId,
                  '*currentUserName' => $this->rodsuser->getUserInfo()['name']),
            array('ruleExecOut')
        );
        $result = json_decode($rule->execute()['ruleExecOut'], true);

        # Get results of uuIsReviewer call.
        if ($result['status'] == 0) {
            $isReviewer = $result['isReviewer'];
        }

        return $isReviewer;
    }
}
