<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Proposal_model extends CI_Model
{
    function submit($data)
    {
        $outputParams = array('*status', '*statusInfo');
        $inputParams = array('*data' => $data);

        $rule = $this->irodsrule->make('uuSubmitProposal', $inputParams, $outputParams);
        $result = $rule->execute();
        return $result;
    }

    function overview()
    {
        $outputParams = array('*result', '*status', '*statusInfo');
        $inputParams = array('*data' => 'bla');

        $rule = $this->irodsrule->make('uuGetProposals', $inputParams, $outputParams);
        $ruleResult = $rule->execute();

	$results = $ruleResult['*result'];

	$status = $ruleResult['*status'];
	$statusInfo = $ruleResult['*statusInfo'];

	$summary = $results[0];
	unset($results[0]);

	$rows = $results;

	$output = array(
		'summary' => $summary,
		'rows' => $rows,
		'status' => $status,
		'statusInfo' => $statusInfo
	);

        return $output;
    }
}
