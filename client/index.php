<?php

include "OAuth.php"; 


$OauthList  = []; 

//Parametres pour instancier l'objet OAuth :
// providerName,$authUrl,$client_id, $client_secret, $redirect_uri,$response_type,$scope,$accessTokenUrl

$Oauth2 = new OAuth('facebook','https://www.facebook.com/v2.10/dialog/oauth','369130728669999','client_secret','http://localhost:8081/callback_facebook','code','public_profile','https://graph.facebook.com/v2.10/oauth/access_token');
$OauthGougueul = new OAuth('google','oauth_url','client_id','client_secret','redirect_uri','response_type','scope','url_pour_identifier_le_token');

// $OauthList[] = $Oauth2; // On push chacun de nos Oauth dans un array
$OauthList[] = $OauthGougueul;

function login($OauthList){
    foreach($OauthList as $OauthEntity){
        echo $OauthEntity->createLoginButton();
    }
}


$route = $_SERVER['REQUEST_URI'];
switch (strtok($route, "?")) {
    case '/login':
        login($OauthList);
        break;
    case '/callback_facebook':
        OAuth::callback($Oauth2);
        break;

    case '/callback_google':
        OAuth::callback($OauthGougueul);
        break;

    default:
        echo '404';
        break;
}
