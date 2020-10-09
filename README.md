# Schibsted account Provider for OAuth 2.0 Client

[![Build Status](https://img.shields.io/travis/schibsted/account-sdk-php.svg)](https://travis-ci.org/schibsted/account-sdk-php)
[![License](https://img.shields.io/packagist/l/schibsted/account-sdk-php.svg)](https://github.com/schibsted/account-sdk-php/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/schibsted/account-sdk-php.svg)](https://packagist.org/packages/schibsted/account-sdk-php)

This package provides Schibsted OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require schibsted/account-sdk-php
```

## Usage

Usage is the same as The League's OAuth client, using `Schibsted\OAuth2\Client\Provider\Schibsted` as the provider.

### Authorization Code Flow

You have to provide some parameters to the provider:

- domain:
   - description: The Schibsted account domain to use, e.g https://login.schibsted.com
- clientId
   - description: The client ID assigned to you by the provider
- clientSecret
   - description: The client password assigned to you by the provider
- redirectUri

```php
$provider = new Schibsted\OAuth2\Client\Provider\Schibsted([
    'domain'       => '{domain}',
    'clientId'     => '{schibsted-client-id}',
    'clientSecret' => '{schibsted-client-secret}',
    'redirectUri'  => 'https://example.com/callback-url'
]);

// Fetch a client token
$token = $provider->getAccessToken('client_credentials');

// Fetch a user token from authorization code
$token = $provider->getAccessToken('authorization_code', [ 'code' => $_GET['code'] ]);

// Fetch Resource owner from user token
$user = $provider->getResourceOwner($token);

// Make an API request to Schibsted account
$req = $provider->getAuthenticatedRequest('GET', 'user/1', $token);
$res = $provider->getParsedResponse($req);

// or to your own service, using the Schibsted account token that you can introspect locally
$req = $provider->getAuthenticatedRequest('GET', 'https://myapi.com/resource/1', $token);
$res = $provider->getParsedResponse($req);

// Refreshing a token
if ($token->hasExpired()) {
    $token = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $token->getRefreshToken()
    ]);
}
```
