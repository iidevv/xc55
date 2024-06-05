<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provider setting
 *
 * @ORM\Entity
 * @ORM\Table (name="qsl_oauth2_client_provider_settings")
 */
class ProviderSetting extends \XLite\Model\Base\NameValue
{
    /**
     * Semi-serialized parameter value representation
     *
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $value;

    /**
     * Provider
     *
     * @var \QSL\OAuth2Client\Model\Provider
     *
     * @ORM\ManyToOne  (targetEntity="QSL\OAuth2Client\Model\Provider", inversedBy="settings")
     * @ORM\JoinColumn (name="provider_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $provider;

    // {{{ Getters / setters

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param Provider $provider
     *
     * @return static
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    // }}}
}
