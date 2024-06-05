<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Provider;

/**
 * Xcart resource owner
 */
class XcartResourceOwner implements \League\OAuth2\Client\Provider\ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var mixed
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return empty($this->response['profile_id']) ? null : $this->response['profile_id'];
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return empty($this->response['login']) ? null : $this->response['login'];
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return $this->response;
    }
}
