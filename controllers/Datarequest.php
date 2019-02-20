<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use JsonSchema\Constraints\Factory;

class Datarequest extends MY_Controller
{
    public function index() {

        // Load CSRF token
        $tokenName = $this->security->get_csrf_token_name();
        $tokenHash = $this->security->get_csrf_hash();

        $viewParams = array(
            'styleIncludes' => array(
                'css/datarequest.css',
            ),
            'tokenName'        => $tokenName,
            'tokenHash'        => $tokenHash,
        );

        loadView('form', $viewParams);
    }

    public function store()
    {
        $arrayPost = $this->input->post();
        # TODO: persist data
    }
}
