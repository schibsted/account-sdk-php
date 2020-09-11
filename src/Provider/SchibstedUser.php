<?php

namespace Schibsted\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class SchibstedUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $userInfo = [];

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->userInfo = $response;
    }

    public function getId()
    {
        return $this->userInfo['sub'];
    }

    public function getUserId()
    {
        return $this->userInfo['legacy_user_id'];
    }

    /**
     * Get the display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->userInfo['preferred_username'];
    }

    /**
     * Get user data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->userInfo;
    }
}
