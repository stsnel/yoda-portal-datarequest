<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Proposal_model extends CI_Model
{
    function submit($data)
    {
        $outputParams = array('*status', '*statusInfo');
        $inputParams = array('*data' => $data);

        $rule = $this->irodsrule->make('iiSubmitProposal', $inputParams, $outputParams);
        $result = $rule->execute();
        return $result;
    }
}
