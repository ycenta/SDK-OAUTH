<?php
echo "fichier SDK OAuth.php ";
class OAuth
{

    public $client_id;
    public $client_secret;
    public $redirect_uri;
    public $callback_uri;
    public $scope;
    public $state;
    public $providerName;
    public $authUrl;
    public $accessTokenUrl;

    public function __construct($providerName,$authUrl,$client_id, $client_secret, $redirect_uri,$response_type,$scope,$accessTokenUrl)
    {
        $this->providerName = $providerName;
        $this->authUrl = $authUrl;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        // $this->callback_uri = $callback_uri;
        $this->response_type = $response_type;
        $this->scope = $scope;
        $this->accessTokenUrl= $accessTokenUrl;

    }

    public function setQueryParam(){ //On set les param qui iront dans l'url (du boutton connecter)
        $queryParams= http_build_query(array(
            "client_id" => $this->client_id,
            "redirect_uri" => $this->redirect_uri,
            "response_type" => $this->response_type,
            "scope" => $this->scope,
            "state" => bin2hex(random_bytes(16))
        ));
        return $queryParams;
    }


    public static function callback(OAuth $OAuth){ //Données renvoyées par le provider à notre url de callback

        $specifParams = [
            "grant_type" => $OAuth->grant_type ?? 'authorization_code',
            "code" => $_GET["code"],
        ];

        $data = http_build_query(array_merge([
            "redirect_uri" => $OAuth->redirect_uri,
            "client_id" => $OAuth->client_id,
            "client_secret" => $OAuth->client_secret
        ], $specifParams));

        $url = $OAuth->accessTokenUrl."?{$data}";

        // url pour facebook :
        // $url = "https://graph.facebook.com/v2.10/oauth/access_token?{$data}";

    
        if($OAuth->providerName == 'facebook'){ 
                //ici pour facebook
            $result = file_get_contents($url);
            $result = json_decode($result, true);
            $accessToken = $result['access_token'];

            //API CALL
            $url = "https://graph.facebook.com/v2.10/me";
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer ' . $accessToken
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $result = json_decode($result, true);
            echo "<br>Hello {$result['name']}";
        }elseif ($OAuth->providerName=="google") {

            $ch = curl_init();
            $tmp_url = $OAuth->accessTokenUrl;
            
            curl_setopt($ch,CURLOPT_URL, $tmp_url);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);
            $result = json_decode($result, true);
            $accessToken = $result['access_token'];

            //API CALL
            $url = "https://www.googleapis.com/oauth2/v3/userinfo?access_token=$accessToken";
            $result = file_get_contents($url);
            $result = json_decode($result, true);
            echo "<br>Hello {$result['name']}";

        }elseif ($OAuth->providerName=="discord") {
            
            $ch = curl_init();
            $tmp_url = $OAuth->accessTokenUrl;
            
            curl_setopt($ch,CURLOPT_URL, $tmp_url);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);
            $result = json_decode($result, true);
            $accessToken = $result['access_token'];

            //API CALL
            $url = "https://discord.com/api/users/@me";
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer ' . $accessToken
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $result = json_decode($result, true);

            echo "<br>Hello {$result['username']}";

        }
     
    }

    public function createLoginButton(){
        
        return "<br><a href='".$this->authUrl.'?'.$this->setQueryParam()."'><button>Se connecter avec ".$this->providerName."</button></a>";
    }

}