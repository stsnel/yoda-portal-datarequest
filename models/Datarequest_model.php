<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Datarequest model
 *
 * @package    Yoda
 * @copyright  Copyright (c) 2019, Utrecht University. All rights reserved.
 * @license    GPLv3, see LICENSE.
 */

class Datarequest_model extends CI_Model
{
    function submit($data)
    {
        $rule = new ProdsRule(
            $this->rodsuser->getRodsAccount(),
            'rule { uuSubmitDatarequest(*data); }',
            array('*data' => $data),
            array('ruleExecOut')
        );

        $result = json_decode($rule->execute()['ruleExecOut'], true);
        return $result;
    }

    function overview($limit, $offset = 0)
    {
        # Get table data from iRODS
        $inputParams = array('*limit' => (int)$limit,
                             '*offset' => (int)$offset);
        $outputParams = array('*result', '*status', '*statusInfo');
        $rule = $this->irodsrule->make('uuGetDatarequests',
                                       $inputParams, $outputParams);
        $ruleResult = $rule->execute();

        # Get additional data (ugly, but multiple queries have to be made
        # because iRODS lacks support for the OR operator)
        $inputParams = array('*limit' => (int)$limit, '*offset' => (int)$offset,
                             '*attributeName' => 'title');
        $outputParams = array('*result', '*status', '*statusInfo');
        $ruleAdditional = $this->irodsrule
                               ->make('uuGetDatarequestsAdditionalFields',
                                      $inputParams, $outputParams);
        $ruleAdditionalResult = $ruleAdditional->execute();

        # Parse the data that we got from iRODS
        $results           = $ruleResult['*result'];
        $resultsAdditional = $ruleAdditionalResult['*result'];
        unset($resultsAdditional[0]);
        $status            = $ruleResult['*status'];
        $statusInfo        = $ruleResult['*statusInfo'];
        $summary           = $results[0];
        unset($results[0]);
        $rows              = $results;

        # Append additional results to $rows
        $i = 1;
        foreach ($rows as $row) {
            $rows[$i]['title'] = $resultsAdditional[$i]['META_DATA_ATTR_VALUE'];
            $i++;
        };

        # Return results to controller
        return array(
                'summary' => $summary,
                'rows' => $rows,
                'status' => $status,
                'statusInfo' => $statusInfo
        );
    }
}
