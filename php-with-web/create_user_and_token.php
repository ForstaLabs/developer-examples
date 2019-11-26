<?php

require 'vendor/autoload.php';

# initialize http client
use GuzzleHttp\Client;

$client = new GuzzleHttp\Client();

$admin_user_auth_token = 'ADMIN_AUTH_TOKEN_HERE';
try {
  # login as admin to retrieve jwt

  $loginResponse = $client->post('https://atlas.forsta.io/v1/login/', [
    'json' => [
      'userauthtoken' => $admin_user_auth_token
    ]
  ]);
  $loginResponseJson = json_decode($loginResponse->getBody()->getContents());
  $jwt = 'JWT ' . $loginResponseJson->token;
} catch (Exception $e) {
  exit("Failed to login: " . $e->getMessage());
}

# use admin jwt to create a new user
$new_user_first_name = 'NEW';
$new_user_last_name = 'USER';
$new_user_tag_slug = 'newusertag';
# response value that we need for the next step
$new_user_id = '';
try {
  $createUserResponse = $client->post('https://atlas.forsta.io/v1/user/', [
    'headers' => [
      'Authorization' => $jwt
    ],
    'json' =>  [
      'first_name' => $new_user_first_name,
      'last_name' => $new_user_last_name,
      'tag_slug' => $new_user_tag_slug,
    ]
  ]);
  $createUserResponseJson = json_decode($createUserResponse->getBody()->getContents());
  $new_user_id = $createUserResponseJson->id;
  echo "New user created\n";
  echo 'Id: ' . $new_user_id . "\n";
  echo 'Tag: ' . $new_user_tag_slug . "\n";
} catch (Exception $e) {
  exit("Failed to create user: " . $e->getMessage());
}

# use the jwt and user id to create an authtoken for the user we just created
try {
  $createUserAuthTokenResponse = $client->post('https://atlas.forsta.io/v1/userauthtoken/', [
    'headers' => [
      'Authorization' => $jwt
    ],
    'json' => [
      'userid' => $new_user_id
    ]
  ]);
  $createUserAuthTokenResponseJson = json_decode($createUserAuthTokenResponse->getBody()->getContents());
  $newUserAuthToken = $createUserAuthTokenResponseJson->token;
  echo 'authtoken: ' . $newUserAuthToken . "\n";
} catch (Exception $e) {
  exit("Failed to create userauthtoken: " . $e->getMessage());
}
