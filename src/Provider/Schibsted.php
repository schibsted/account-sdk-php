<?php

namespace Schibsted\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

class Schibsted extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $domain;
    protected $apiVersion = '2';

    /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @throws UnexpectedValueException
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *     Individual providers may introduce more options, as needed.
     * @param array $collaborators An array of collaborators that may be used to
     *     override this provider's default behavior. Collaborators include
     *     `grantFactory`, `requestFactory`, and `httpClient`.
     *     Individual providers may introduce more collaborators, as needed.
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->domain = rtrim($options['domain'], '/');
        unset($options['domain']);
        if (substr($this->domain, 0, 4) !== 'http') {
            throw new UnexpectedValueException('Domain must include protocol');
        }

        parent::__construct($options, $collaborators);
    }

    protected function domain()
    {
        return $this->domain;
    }

    /**
     * Get authorization url to begin OAuth flow.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->domain() . '/oauth/authorize';
    }

    /**
     * Get access token url to retrieve token.
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain() . '/oauth/token';
    }

    /**
     * Returns the url to retrieve the resource owners's profile/details.
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->domain() . '/oauth/userinfo';
    }

    /**
     * Returns the default headers used by this provider.
     *
     * @return array
     */
    protected function getDefaultHeaders()
    {
        return [
            'X-OIDC' => 'v1'
        ];
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['openid', 'profile', 'email', 'address', 'phone', 'offline_access'];
    }

    /**
     * Returns the string used to separate scopes.
     *
     * @return string
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Checks Schibsted account API response for errors.
     *
     * @throws IdentityProviderException
     *
     * @param ResponseInterface $response
     * @param array|string      $data     Parsed response data
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (in_array($response->getStatusCode(), [400, 401]) &&
            is_string($data['error']) &&
            (is_string($data['error_description']) || (is_string($data['type']) && $data['type'] === 'OAuthException'))
        ) {
            throw new IdentityProviderException(
                $data['error'],
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * Returns authorization parameters based on provided options.
     * Schibsted account does not use the 'approval_prompt' param and here we remove it.
     *
     * @param array $options
     *
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options)
    {
        $params = parent::getAuthorizationParameters($options);
        unset($params['approval_prompt']);
        $params['prompt'] = isset($options['prompt']) ? $options['prompt'] : 'select_account';
        return $params;
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param array       $response
     * @param AccessToken $token
     *
     * @return SchibstedUser
     */
    public function createResourceOwner(array $response, AccessToken $token)
    {
        return new SchibstedUser($response);
    }

    /**
     * Returns the key used in the access token response to identify the resource owner.
     *
     * @return string|null Resource owner identifier key
     */
    protected function getAccessTokenResourceOwnerId()
    {
        return 'id_token';
    }

    /**
     * Wrapper to make request against Schibsted account
     *
     * @param  string $method
     * @param  string $path
     * @param  League\OAuth2\Client\Token\AccessTokenInterface|string $token
     * @param  array $options Any of "headers", "body", and "protocolVersion".
     * @return Psr\Http\Message\RequestInterface
     */
    public function getAuthenticatedRequest($method, $url, $token, $options = [])
    {
        if (substr($url, 0, 4) !== 'http') {
            $path = ltrim($url, '/');
            $url = $this->domain() . "/api/{$this->apiVersion}/${path}";
        }
        return parent::getAuthenticatedRequest($method, $url, $token, $options);
    }
}
