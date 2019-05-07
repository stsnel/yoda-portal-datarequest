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

    function overview($proposalId, $limit, $offset = 0)
    {
        $inputParams = array('*proposalId' => $proposalId, '*limit' => (int)$limit, '*offset' => (int)$offset);
        $outputParams = array('*result', '*status', '*statusInfo');

        $rule = $this->irodsrule->make('uuGetDatarequests', $inputParams, $outputParams);

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
