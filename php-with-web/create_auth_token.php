<?php

    require 'vendor/autoload.php';

    # initialize http client
    use GuzzleHttp\Client;
    $client = new GuzzleHttp\Client();
    
    # login using your username and password
    $admin_tag_slug = '@USERNAME:ORGNAME';
    $admin_password = 'PASSWORD';

    $loginResponse = $client->post('https://atlas.forsta.io/v1/login/', [
        'json' => [
            'fq_tag' => $admin_tag_slug,           # For user/password login
            'password' => $admin_password          # For user/password login
        ]
    ]);
    $loginResponseJson = json_decode($loginResponse->getBody()->getContents());
    $jwt = 'JWT ' . $loginResponseJson->token;
    echo 'jwt: ' . $jwt . "\n";

    # create a new auth token for your account
    $authTokenResponse = $client->post('https://atlas.forsta.io/v1/userauthtoken/', [
        'headers' => [
            'Authorization' => $jwt
        ],
    ]);
    $authTokenResponseJson = json_decode($authTokenResponse->getBody()->getContents());
    # save this token somewhere safe
    # you will need it whenever you want to embed the messenger
    # or make modifications to your org
    $userauthtoken = $authTokenResponseJson->token;
    echo 'userauthtoken: ' . $userauthtoken . "\n";

?>