<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Proposal model
 *
 * @package    Yoda
 * @copyright  Copyright (c) 2019, Utrecht University. All rights reserved.
 * @license    GPLv3, see LICENSE.
 */

class Datarequest_model extends CI_Model
{
    function submit($data, $proposalId)
    {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuSubmitDatarequest(*data, *proposalId); }',
            array('*data' => $data, '*proposalId' => $proposalId),
            array('ruleExecOut')
        );

        $result = $rule->execute();
        return $result;
    }
}
