<?php 
require 'vendor/autoload.php';
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;


# initialize http client
use GuzzleHttp\Client;
$client = new GuzzleHttp\Client();

$forsta_api_path = "https://atlas.forsta.io/v1/";

# login as admin to retrieve jwt
# Either use auth_token OR username and password
$admin_user_auth_token = '<admin authtoken>';
$admin_username = '@<user>:<org>';
$admin_password = '<password>';

# User to get authtoken
$user_tag_slug = '@test1234';

try {
  $loginResponse = $client->post($forsta_api_path . 'login/', [
      'json' =>  [
          'userauthtoken' => $admin_user_auth_token
#          'fq_tag' => $admin_username,
#          'password' => $admin_password
      ]
  ]);
}
catch (Exception $e) {
  exit("Failed to login: " . $e->getMessage());
}

echo("Successfully connected\n");
$loginResponseJson = json_decode($loginResponse->getBody()->getContents());
$jwt = 'JWT ' . $loginResponseJson->token;

# use admin jwt to get the user id
try {
  $getUserResponse = $client->post($forsta_api_path . 'tagmath/', [
      'headers' => [
          'Authorization' => $jwt
      ],
      'json' =>  [
          "expressions" => [$user_tag_slug]
      ]
  ]);
}
catch (Exception $e) {
  exit("Failed to get user ids: " . $e->getMessage());
}

echo("User Id Retrieved\n");
$getUserResponseJson = json_decode($getUserResponse->getBody()->getContents());

# Note this assumes the tag given results in one and only one userid
$newUserId = $getUserResponseJson->results[0]->userids[0];

# create a new auth token for the user we just created
$tokenDescription = 'Auth Token Description';
try{
  $postAuthTokenResponse = $client->post($forsta_api_path . 'userauthtoken/', [
      'headers' => [
          'Authorization' => $jwt
      ],
      'json' =>  [
          'userid' => $newUserId,
          'description' => $tokenDescription
      ]
  ]);  
}
catch (Exception $e) {
  exit("Failed to create user authtoken: " . $e->getMessage());
}

$getUserResponseJson = json_decode($postAuthTokenResponse->getBody()->getContents());

echo("User authtoken created. Please save the following:\n");
echo($getUserResponseJson->token . "\n");

?>
