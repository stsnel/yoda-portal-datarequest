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
        $outputParams = array('*status', '*statusInfo');
        $inputParams = array('*data' => $data, '*proposalId' => $proposalId);

        $rule = $this->irodsrule->make('uuSubmitDatarequest', $inputParams, $outputParams);
        $result = $rule->execute();
        return $result;
    }
}
