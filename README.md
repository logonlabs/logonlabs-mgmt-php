# LogonLabs PHP

The official LogonLabs PHP library.
## Download

    composer require logonlabs/logon-manage-sdk-php
## LogonLabs API

- For the full Developer Documentation please visit: https://app.logonlabs.com/api-manage/

---

### Instantiating a new client

- The `LOGONLABS_API_ENDPOINT` should be set to `https://manage.logonlabs.com`.

Create a new instance of `LogonClient`.  
```php
<?php
use LogonLabs\Manage\LogonClient as LogonClient;
$logonClient = new LogonClient(array(
    'username' => '{USERNAME}',
    'password' => '{PASSWORD}',
    'api_path' => '{LOGONLABS_API_ENDPOINT}',
    'gateway_id' => '{LOGONLABS_GATEWAY_ID}',
));
```

### The Logon Response and Error Handling
The success() function determines whether there were any errors from the request.  If it returns true then it is safe to get the body of the response.  Otherwise error handling/checking can be done via getStatus().
```php
if ($logonResponse->success()) {
    $data = $logonResponse->getBody();
} else {
    $status = $logonResponse->getStatus();
}
```

### Logon Paging
All requests that return a list of items are paged.  Most of the time the results will all end up on a single page but in the case where there are more than a single page of items to be returned, the consumer will have to iterate over the paged requests to get all results.  The request allows the consumer to specify the page they would like along with the number of items to return.  The response provides paging details that can be used to iterate over the remaining items.  In the example below getApps() has been used to demonstrate one method to page through all of a user's Apps.
```php
$logonResponse = $logonClient->getApps(array(
   'page' => '1', 
   'page_size' => '100' //the default
));

if($logonResponse->success()){

    $pagedData = $logonResponse->getBody();
    $pageSize = $pagedData->getPageSize();
    $totalPages = $pagedData->getTotalPages();
    $totalItems = $pagedData->getTotalItems();
    $page = $pagedData->getCurrentPage();
    $results = $pagedData->getResults();

    while(++$page <= $totalPages) {

        $logonResponse = $logonClient->getApps(array(
            'page' => $page,
            'page_size' => $pageSize
        ));
        
        if($logonResponse->success()) {
            $pagedData = $logonResponse->getBody();
            array_push($results, $pagedData->getResults());
        }
    }
}
```

### Useful Properties
Some calls require the user_id of a given user to be passed.  If it is the current authenticated user's ID that is required getProfile() can be used to retrieve that value.
```php
//Get my User Id
$responseBody = $logonClient->getProfile()->getBody();
$userId = $responseBody->getUserId();

```

### App Management
An App object represents a Logon Labs SSO configuration. In order for a customer to start performing brokered SSO logins on their login screen, an App must be provisioned for them.

#### CreateApp

```php
$responseBody = $logonClient->createApp(array(
    'name' => 'Sample App Name',
    'gateway_id' => 'ID-for-the-gateway-instance'
))->getBody();
$appId = $responseBody->getAppId();
```

#### GetApp

```php
$responseBody = $logonClient->getApp($appId);
$app = $responseBody->getBody();

$appId = $app->getAppId();
$name = $app->getName();
$createdDate = $app->getCreatedDate());
$callbackUrl = $app->getCallbackUrl();
$corsWhitelist = $app->getCorsWhitelist();
$paymentId = $app->getPaymentId();
$callbackUrlWhitelist = $app->getCallbackUrlWhitelist();
$destinationUrlWhitelist = $app->getDestinationUrlWhitelist();
$plan = $app->getPlan();
$eventLogsEnabled = $app->getEventLogsEnabled();
$ipRestrictionEnabled = $app->getIpRestrictionEnabled();
$ipRestrictionMode = $app->getIpRestrictionMode();
$regionRestrictionEnabled = $app->getRegionRestrictionEnabled();
$regionRestrictionMode = $app->getRegionRestrictionMode();
$timeRestrictionEnabled = $app->getTimeRestrictionEnabled();
$timeRestrictionMode = $app->getTimeRestrictionMode();
$timeZone = $app->getTimeZone();
```

#### GetApps

```php
$responseBody = $logonClient->getApps($optionalPagingOptions)->getBody(); 
$apps = $responseBody->getResults();
foreach($apps as $app) {
    //refer to GetApp for details of the App Object
    //refer to Logon Paging for details on getting all pages of the response
}
```

#### UpdateApp

```php
$logonClient->updateApp($appId, array(
    'name' => 'Sample App Name',
    'callback_url' => 'https://example.com/callback',
    'cors_whitelist' => ['https://example.com/whitelistItem'],
    'payment_id' => 'example_payment_id',
    'callback_url_whitelist' => ['https://example.com/callbackWhitelistItem'],
    'destination_url_whitelist' => ['https://example.com/destinationWhitelistItem'],
    'plan' => 'free/basic/premium',
    'event_logs_enabled' => true,
    'ip_restriction_enabled' => false,
    'ip_restriction_mode' => 'blacklist',
    'region_restriction_enabled' => false,
    'region_restriction_mode' => 'blacklist',
    'time_restriction_enabled' => false,
    'time_restriction_mode' => 'blacklist',
    'time_zone' => 'IANA_formatted_timezone'
));
```

#### RemoveApp

```php
$logonClient->removeApp($appId);
```

### User Management
A User must be granted access to an App in order to access configuration. These API calls are especially useful when provisioning an App for a client but also want to give them the ability to manage configuration of the App themselves after. The following routes allow users to have their access granted, removed, or modified.

#### AddAppUser

```php
$responseBody = $logonClient->addAppUser($appId, array(
    'email_address' => $email,
    'role' => 'administrator'
))->getBody();

$userId = $responseBody->getUserId();
```

#### GetAppUsers

```php
$responseBody = $logonClient->getAppUsers($appId, $optionalPagingOptions)->getBody();

$appUsers = $responseBody->getResults();

foreach ($appUsers as $appUser) {
    $userId = $appUser->getUserId();
    $emailAddress = $appUser->getEmailAddress();
    $role = $appUser->getRole();
}

```

#### UpdateAppUser

```php

//Upgrade User to Owner 
$logonClient->updateAppUser($appId, $userId, array(
    'email_address' => $email,
    'role' => 'owner'
));

```

#### RemoveAppUser

```php
$logonClient->removeAppUser($appId, $userId);

```

### App Secret Management
App Secrets are required by the clients that consume to the Gateway API.  This secret verifies to the Logon Labs Gateway that the caller making requests is authorized to do so.

#### CreateAppSecret

```php
$responseBody = $logonClient->createAppSecret($appId)->getBody();
$secretId = $responseBody->getSecretId();
```

#### GetAppSecrets

```php
$responseBody = $logonClient->getAppSecrets($appId, $optionalPagingOptions)->getBody();
$appSecrets = responseBody->getResults();

foreach ($appSecrets as $appSecret) {
    $secretId = $userSecret->getSecretId();
    $secret = $userSecret->getSecret();
}
```

#### RemoveAppSecret

```php
$logonClient->removeAppSecret($appId, $secretId);
```

### User Secret Management
User Secrets work similarly to App Secrets but are reusable and can be applied to any number of Apps.  This allows for less secrets to be created and managed but does require an additional API call to Assign the secret to an App.

#### CreateUserSecret

```php
$responseBody = $logonClient->createUserSecret($userId)->getBody();
$secretId = $responseBody->getSecretId();
```

#### GetUserSecrets

```php
$responseBody = $logonClient->getUserSecrets($userId)->getBody();
$userSecrets = $responseBody->getResults();

foreach ($userSecrets as $userSecret) {
    $secretId = $userSecret->getSecretId();
    $secret = $userSecret->getSecret();
}
```

#### RemoveUserSecret

```php
$logonClient->removeUserSecret($userId, $secretId);
```

#### AssignUserSecret

```php
$logonClient->assignUserSecret($userId, $secretId, $appId);
```

#### UnassignUserSecret

```php
$logonClient->unassignUserSecret($userId, $secretId, $appId);
```

#### GetUserSecretApps

```php
$responseBody = $logonClient->getUserSecretApps($userId, $secretId, $optionalPagingOptions)->getBody();
$apps = $responseBody->getResults();

foreach($apps as $app) {
    //refer to GetApps for details on properties returned
}
```

### Provider Management
A Provider object is used to represent a single method for logging in. For example to have Google and Microsoft support there must be 2 providers created. Logon Labs provides two separate types of Provider experiences: Social and Enterprise. The first, Social, is a more on-rails experience that only requires knowing the Client Id and Secret from your chosen Identity Provider. The second, Enterprise, requires more knowledge of the inner workings of an Identity Provider but also provides more flexibility and control over the experience. Although the same API call is used to create either version of Provider examples are shown for each as they differ significantly in what is required.
Custom Routes are available to create, get, update, and remove providers.

#### CreateSocialProvider

```php
$responseBody = $logonClient->createSocialProvider(array(
    'identity_provider' => 'google',
    'protocol' => 'oauth',
    'name' => 'sample provider',
    'description' => 'description',
    'client_id' => 'client_sample_id',
    'client_secret' => 'client_sample_secret'
))->getBody();

$identityProviderId = $responseBody->getIdentityProviderId();
```

#### CreateEnterpriseProvider

```php
//OAUTH
$result_1 = $logonClient->createEnterpriseProvider(array(
    'identity_provider' => 'google',
    'protocol' => 'oauth',
    'name' => 'google oauth provider',
    'description' => 'description',
    'client_id' => 'client_sample_id',
    'client_secret' => 'client_sample_secret',
    'login_url' => 'https://www.example.com/google/login',
    'token_url' => 'http://www.example.com/google/token',
    'login_button_image_uri' => '',
    'login_icon_image_uri' => '',
    'login_background_hex_color' => '#000000',
    'login_text_hex_color' => '#ffffff'
))->getBody();
$oauthIdentityProviderId = $result_1->getIdentityProviderId();

//SAML
$result_2 = $logonClient->createEnterpriseProvider(array(
    'identity_provider' => 'google',
    'protocol' => 'saml',
    'name' => 'google saml provider',
    'description' => 'description',
    'client_id' => 'client_sample_id',
    'identity_provider_certificate' => '',
    'login_url' => 'https://www.example.com/google/login',
    'login_button_image_uri' => '',
    'login_icon_image_uri' => '',
    'login_background_hex_color' => '#000000',
    'login_text_hex_color' => '#ffffff'
))->getBody();
$samlIdentityProviderId = $result_2->getIdentityProviderId();
```

#### UpdateProvider

```php
//Social
$logonClient->updateProvider($identityProviderId, array(
    'name' => 'google saml provider',
    'description' => 'description',
    'client_id' => 'client_sample_id',
    'client_secret' => 'client_sample_secret'
));

//Enterprise OAuth
$logonClient->updateProvider($identityProviderId, array(
    'name' => 'google oauth provider',
    'description' => 'description',
    'client_id' => 'client_sample_id',
    'client_secret' => 'client_sample_secret',
    'login_url' => 'https://www.example.com/google/login',
    'token_url' => 'http://www.example.com/google/token',
    'login_button_image_uri' => '',
    'login_icon_image_uri' => '',
    'login_background_hex_color' => '#000000',
    'login_text_hex_color' => '#ffffff'
));

//Enterprise SAML
$logonClient->updateProvider($identityProviderId, array(
    'name' => 'google saml provider',
    'description' => 'description',
    'client_id' => 'client_sample_id',
    'identity_provider_certificate' => '',
    'login_url' => 'https://www.example.com/google/login',
    'login_button_image_uri' => '',
    'login_icon_image_uri' => '',
    'login_background_hex_color' => '#000000',
    'login_text_hex_color' => '#ffffff'
));
```

#### GetProviderDetails

```php
$responseBody = $logonClient->getProviderDetails($identityProviderId)->getBody();

//properties common to all provider types
$providerId = $responseBody->getProviderId();
$identityProvider = $responseBody->getIdentityProvider();
$protocol = $responseBody->getProtocol();
$type = $responseBody->getType();
$name = $responseBody->getName();
$description = $responseBody->getDescription();
$clientId = $responseBody->getClientId();

if($protocol == 'oauth') {
    $clientSecret = $responseBody->getClientSecret();
} 

//properties available only for enterprise types
if ($type == 'enterprise') {

    $loginUrl = $responseBody->getLoginUrl();
    $loginButtonImageUri = $responseBody->getLoginButtonImageUri();
    $loginIconImageUri = $responseBody->getLoginIconImageUri();
    $loginBackgroundHexColor = $responseBody->getLoginBackgroundHexColor();
    $loginTextHexColor = $responseBody->getLoginTextHexColor();

    if($protocol == 'oauth') {
        $tokenUrl = $responseBody->getTokenUrl();
    }
    else if ($protocol == 'saml') {
        $identityProviderCertificate = $responseBody->getIdentityProviderCertificate();
        $serviceProviderCertificate = $responseBody->getServiceProviderCertificate();
    }


}
```

#### GetProviders

```php
$responseBody = $logonClient->getProviders($optionalPagingOptions)->getBody();
$providers = $responseBody->getResults();

foreach($providers as $provider) {

    $name = $provider->getName();
    $description = $provider->getDescription();
    $providerId = $provider->getProviderId();
    $identityProvider = $provider->getIdentityProvider();
    $protocol = $provider->getProtocol();
    $sandbox = $provider->getSandbox();
    $type = $provider->getType();

    if($type == 'enterprise') {
        $loginButtonImageUri = $provider->getLoginButtonImageUri();
        $loginBackgroundHexColor = $provider->getLoginBackgroundHexColor();
        $loginIconImageUri = $provider->getLoginIconImageUri();
        $loginTextHexColor = $provider->getLoginTextHexColor();
    }
}
```

#### RemoveProvider

```php
$logonClient->removeProvider($identityProviderId);
```

#### ShareProvider
```php
$logonClient->shareProvider($identityProviderId, $userId);
```

#### UnshareProvider
```php
$logonClient->unshareProvider($identityProviderId, $userId);
```

### Provider Assignment
In order for an App to have a Provider available for it's use it must be assigned to it and then enabled.
Routes are provided to grant and revoke access to an identity Provider (this makes the Provider available to the App) and then enable/disable it (turn on or off for an App).

#### AssignProvider

```php
$logonClient->assignProvider($identityProviderId, $appId);
```

#### UnassignProvider

```php
$logonClient->unassignProvider($identityProviderId, $appId);
```

#### EnableAppProvider

```php
$logonClient->enableAppProvider($appId, $identityProviderId);
```

#### DisableAppProvider

```php
$logonClient->disableAppProvider($appId, $identityProviderId);
```

#### GetAppProviders

```php
$responseBody = $logonClient->getAppProviders($appId, $optionalPagingOptions)->getBody();
$appProviders = $responseBody->getResults();

foreach($appProviders as $appProvider) {
    //refer to GetProviders for details on properties returned
}
```
