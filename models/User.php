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
}
