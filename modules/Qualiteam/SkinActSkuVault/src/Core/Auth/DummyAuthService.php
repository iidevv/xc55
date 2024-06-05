<?php

namespace Qualiteam\SkinActSkuVault\Core\Auth;

use Qualiteam\SkinActSkuVault\Core\Configuration\Configuration;

class DummyAuthService implements AuthService
{
    private Configuration $configuration;

    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public function getMiddleWare(): callable
    {
        return function () {
            return $this->configuration->getBasicAuthUser() . ':' . $this->configuration->getBasicAuthPassword();
        };
    }
}
