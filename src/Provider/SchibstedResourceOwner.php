<?php

namespace Schibsted\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class SchibstedResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    protected $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getValueByKey($this->response, 'sub');
    }

    /**
     * Returns user_id of the resource owner
     *
     * @return string|null
     */
    public function getUserId()
    {
        return $this->getValueByKey($this->response, 'legacy_user_id');
    }

    /**
     * Returns email address of the resource owner
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }

    /**
     * Returns full name of the resource owner
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getValueByKey($this->response, 'name');
    }

    /**
     * Returns display name of the resource owner
     *
     * @return string|null
     */
    public function getPreferredUsername()
    {
        return $this->getValueByKey($this->response, 'preferred_username');
    }

    /**
     * Returns picture url of the resource owner
     *
     * @return string|null
     */
    public function getPictureUrl()
    {
        return $this->getValueByKey($this->response, 'picture');
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return $this->response;
    }
}
