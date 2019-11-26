## forsta-messenger setup example ##

This example assumes you have two different types of users in your system:

  - Support Users
  - Customers
  
In your user database you will need to add the following string fields:

  - forsta username
  - forsta user authtoken

Where ever you store environmental values in your system you will need to store:

  - forsta org name
  - forsta admin username
  - forsta admin authtoken

0. To run the example code install composer and php. Run 'composer install' to download dependencies
then 'php -S [HOSTNAME]' to run a php server to host the files.

1. To start manually create your Forsta organization using the developer dashboard at:

https://app.forsta.io/join

When you create a new account you also create an administrator user who you can login as an org.
After creating a forsta organization save the forsta org name and forsta admin username with
your environment variables.

2. Create and save a forsta admin authtoken. This will be used to authenticate the account 
you just created with the forsta atlas server. This can be done either by going to the developer
console at https://app.forsta.io/authtokens or using the Forsta Atlas API. For a complete example
on generating authtokens using the Atlas API see:

create_auth_token.php

Once created the authtoken cannot be retrieved from the system. However, new authtokens can be generated
in the future. Copy it and save it to your environment variables.

3. Use the forsta admin authtoken to generate individual forsta user accounts for your support users
and customers. For an example on generating users and their authtokens see:

create_user_and_token.php

After generating a user the tag_slug should be saved in the database as the forsta username and
the authtoken generated should be saved as the forsta user authtoken. Note: Do NOT set an email
address when creating users. The email address is used for security notifications.
You should only set an email address for admins and other accounts that can receive forsta email
notifications.

It's worth noting that the user created in this step will lack admin privileges in the org. As a result, 
they won't be able to create new users or modify the org's information in any way. The only permissions 
a non-admin user (and their authtoken as a result) has, are modifying their own profile, creating personal tags, 
or creating authtokens for themselves.

4. Once users are created and configured in the system a JWT, JSON Web Token, is required to authenticate
a user with the Forsta Embedded client. A JWT is a secret value which can be encoded with a small amount of data.
In this case the JWT is encoded with the personal authentication information of the user who it is for. In 
Forsta ecosystem JWTs are used both with the Forsta Atlas API and the Forsta Embedded Client. JWTs can
expire so should not be saved in your database. Instead a JWT should be generated using the Forsta Atlas
login API as needed. For an example on how to get a JWT see:

embed_client.php

The code after the comment "# login to retrieve jwt" shows how to get a user's Forsta JWT using their
Forsta authtoken. The statement 'YOUR_AUTH_TOKEN_HERE' should be replaced with code to retrieve your
current users authtoken from the database.

5. The retrieved JWT is then used to authenticate a user with the Forsta Embedded Client. For a complete
example that authenticates a user and starts the Embedded Client see:

embed_client.php

Once configured this code will take the following steps:

- Retrieve a JWT for a given user
- Start and configure the embedded client with the users JWT
- Display the client when it has initialized
- Display the client if provisioning is needed
- Allow new message alerts to be handled

6. Several embedded client functions need to be configured to handle your specific system.
In the embed_client.php css visability is used to hide and display the messenger as needed.
To control the messenger in a different way you need to modify:

- ```<div id="my-messenger" style="width: 80%; height: 80%; visability: none"></div>```
- function displayMessenger()

Additionally there are multiple event listeners that allow you to customize how your system responds to
events that occur in the messenger.

The onLoaded callback is configured when the messenger is setup. It is called when the messenger
has finished loading. In the example code the messenger is hidden while loading and then displayed
when the onLoaded callback is triggered.

For provisioning events there is provisioningrequired, provisioningerror and provisioningdone.
Provisioning is the case where a user is using the messenger for either the first time or on
a new client. Before they can send or recieve messages on that client they need to provision
their identity key. Provisioning occurs before the messenger triggers the onload callback and requires
user interaction. In the example code the messenger is displayed if provisioning is needed and hidden
when it is finished.

When messages arrive in the messenger they trigger thread-message. In the example code the
messenger handles the callback but does nothing. You can configure the function to do a variety of
behavior from displaying the messenger to displaying a pending messages icon.

For detailed documentation of the above features see:

- Atlas API Docs - http://atlas.forsta.io/doc/
- Embedded Client Docs & Tutorials - https://forstalabs.github.io/forsta-messenger-client/docs/forsta-messenger-client/LATEST/index.html

