<?php

namespace Schibsted\OAuth2\Client\Test\Provider;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Schibsted\OAuth2\Client\Provider\Schibsted as OauthProvider;
use RuntimeException;

class SchibstedTest extends TestCase
{
    
    const DOMAIN = 'http://id.localhost';

    protected $config = [
        'domain'       => self::DOMAIN,
        'clientId'     => 'mock_client_id',
        'clientSecret' => 'mock_secret',
        'redirectUri'  => 'none',
    ];

    public function testGetAuthorizationUrl()
    {
        $provider = new OauthProvider($this->config);
        $url = $provider->getAuthorizationUrl();
        $parsedUrl = parse_url($url);

        $expectedHost = parse_url(self::DOMAIN)['host'];
        $this->assertEquals($expectedHost, $parsedUrl['host']);
        $this->assertEquals('/oauth/authorize', $parsedUrl['path']);

        parse_str($parsedUrl['query'], $q);
        $scope = explode(' ', $q['scope']);
        $this->assertContains('openid', $scope);
        $this->assertContains('email', $scope);
        $this->assertContains('offline_access', $scope);
    }

    public function testGetUrlAccessToken()
    {
        $provider = new OauthProvider($this->config);
        $url = $provider->getBaseAccessTokenUrl([]);
        $parsedUrl = parse_url($url);

        $expectedHost = parse_url(self::DOMAIN)['host'];
        $this->assertEquals($expectedHost, $parsedUrl['host']);
        $this->assertEquals('/oauth/token', $parsedUrl['path']);
    }

    public function testGetUrlUserDetails()
    {
        $provider = new OauthProvider($this->config);

        $accessTokenDummy = $this->getAccessToken();

        $url = $provider->getResourceOwnerDetailsUrl($accessTokenDummy);
        $parsedUrl = parse_url($url);

        $expectedHost = parse_url(self::DOMAIN)['host'];
        $this->assertEquals($expectedHost, $parsedUrl['host']);
        $this->assertEquals('/oauth/userinfo', $parsedUrl['path']);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|AccessToken
     */
    private function getAccessToken()
    {
        return $this->getMockBuilder(AccessToken::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
