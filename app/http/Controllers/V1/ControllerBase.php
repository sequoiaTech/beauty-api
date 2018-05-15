<?php


namespace App\Http\Controllers\V1;


use App\Providers\Components\AesTrait;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Exception;

class ControllerBase extends Controller
{

    use AesTrait;


    public $uid;


    public $data;


    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
    }


    public function initialize()
    {
        // check secret key
        $key = $this->session->get('key');
        if (!$key) {
            $output = [
                'code'    => 400,
                'message' => 'failure, missing secret key'
            ];
            $this->response->setJsonContent($output)->send();
            exit();
        }

        // check argv
        $iv = base64_decode($this->request->getHeader('Xt-Iv'));
        if (!$iv) {
            $output = [
                'code'    => 400,
                'message' => 'failure, missing argv'
            ];
            $this->response->setJsonContent($output)->send();
            exit();
        }
        $raw = base64_decode($this->request->getRawBody());

        // decrypt
        try {
            $decrypt = $this->decrypt($key, $iv, $raw);
        } catch (Exception $e) {
            $output = [
                'code'    => 400,
                'message' => 'failure, decrypt error'
            ];
            $this->response->setJsonContent($output)->send();
            exit();
        }
        parse_str($decrypt, $this->data);
    }


    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
    }

}