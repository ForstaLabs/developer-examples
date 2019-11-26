<?php

require 'vendor/autoload.php';

# initialize http client
use GuzzleHttp\Client;

$client = new GuzzleHttp\Client();

# login to retrieve jwt
$user_auth_token = 'YOUR_AUTH_TOKEN_HERE';
try {
  $loginResponse = $client->post('https://atlas.forsta.io/v1/login/', [
    'json' => [
      'userauthtoken' => $user_auth_token
    ]
  ]);
  $loginResponseJson = json_decode($loginResponse->getBody()->getContents());
  $jwt = $loginResponseJson->token;
} catch (Exception $e) {
  exit("Failed to login: " . $e->getMessage());
}

?>

<script src="https://forstalabs.github.io/forsta-messenger-client/dist/forsta-messenger-client.min.js"></script>

<!-- Div that contains the Forsta messenger client -->
<div id="my-messenger" style="width: 80%; height: 80%; visability: hidden"></div>
<p id="status-message">The messenger is currently loading...<p>

<script>
  function onProvisioningRequired() {
    displayMessenger();
  }

  function onProvisioningError() {
    alert("Provisioning Failed!");
  }

  function onProvisioningDone() {
    hideMessenger();
  }

  async function onMessage(details) {
    // Add code to notify of messages here
  }

  function displayMessenger() {
    const statusMsg = document.getElementById("status-message");
    const messenger = document.getElementById("my-messenger");
    statusMsg.style.visibility = "hidden";
    messenger.style.visibility = "visible";
  }
  
  function hideMessenger() {
    const statusMsg = document.getElementById("status-message");
    const messenger = document.getElementById("my-messenger");
    statusMsg.style.visibility = "visible";
    messenger.style.visibility = "hidden";
  }

  async function onLoaded(client) {
    displayMessenger();
  }

  // Configure the Forsta messenger client to use the div with id my-messenger
  // And connect as the users specified by the JWT
  const myClient = new forsta.messenger.Client(document.getElementById('my-messenger'), {
    jwt: '<?php echo $jwt; ?>'
  }, {
    onLoaded: onLoaded,
    showNav: true,
    showHeader: true
  });

  // Set callbacks to handle cases where the client needs to be provisioned.
  myClient.addEventListener('provisioningrequired', onProvisioningRequired);
  myClient.addEventListener('provisioningerror', onProvisioningError);
  myClient.addEventListener('provisioningdone', onProvisioningDone);

  // Set a callback for when a new message arrives so we can alert
  myClient.addEventListener('thread-message', onMessage);
</script>