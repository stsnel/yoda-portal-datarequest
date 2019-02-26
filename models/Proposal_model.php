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

    function overview($limit, $offset = 0)
    {
	$ruleBody = <<<'RULE'
myRule {
	*l = int(*limit);
	*o = int(*offset);

	uuGetProposals(*l, *o, *result, *status, *statusInfo)
}
RULE;

	$iRodsAccount = $this->rodsuser->getRodsAccount();

	$rule = new ProdsRule(
		$iRodsAccount,
		$ruleBody,
		array(
			"*limit" => $limit,
			"*offset" => $offset
		),
		array(
			"*result", "*status", "*statusInfo"
		)
	);

        $ruleResult = $rule->execute();

	$results = json_decode($ruleResult['*result'], true);

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
