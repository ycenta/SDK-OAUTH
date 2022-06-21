<?php
class OAuth
{

    public $client_id;
    public $client_secret;
    public $redirect_uri;
    public $callback_uri;

    public function __construct($client_id, $client_secret, $redirect_uri, $callback_uri)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        $this->callback_uri = $callback_uri;
    }

    public function callback($grant_type,$code){
        $specifParams = [
            "grant_type" => $grant_type,
            "code" => $code,
        ];

        $data = http_build_query(array_merge([
            "redirect_uri" => $this->$redirect_uri,
            "client_id" => $this->$client_id,
            "client_secret" => $this->$client_secret
        ], $specifParams));
    }

}