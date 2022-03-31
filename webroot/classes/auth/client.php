<?php

/**
 * OAuth 2.0 Client
 */

namespace Auth;

use FSA\Neuron\HttpResponse,
    AppException;

abstract class Client {

    protected $response_code_url;
    protected $response_token_url;
    private $client_id;
    private $client_secret;

    public function __construct($config) {
        if(isset($config['id'])) {
            $this->client_id=$config['id'];
        } else {
            var_dump($config);
            throw new AppException('');
        }
        $this->client_secret=$config['secret'];
    }

    public function requestCode(string $redirect_uri, string $scope=null, string $state=null): void {
        $query_data=[
            'client_id'=>$this->client_id,
            'redirect_uri'=>$redirect_uri,
            'response_type'=>'code'
        ];
        if(!is_null($scope)) {
            $query_data['scope']=$scope;
        }
        if(!is_null($state)) {
            $query_data['state']=$state;
        }
        HttpResponse::redirection($this->response_code_url.'?'.http_build_query($query_data));
    }

    public function getToken(string $redirect_uri, string $code) {
        $query_data=[
            'client_id'=>$this->client_id,
            'client_secret'=>$this->client_secret,
            'redirect_uri'=>$redirect_uri,
            'code'=>$code,
            'grant_type'=>'authorization_code'
        ];
        $context=stream_context_create([
            'http'=>array(
                'method'=>'POST',
                'header'=>'Content-Type: application/x-www-form-urlencoded'.PHP_EOL,
                'content'=>http_build_query($query_data),
            ),
        ]);
        $token=file_get_contents($this->response_token_url, false, $context);
        return json_decode($token);
    }

}
