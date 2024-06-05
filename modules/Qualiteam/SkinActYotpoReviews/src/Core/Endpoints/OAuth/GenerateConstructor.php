<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth\Params\SetClientIdInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth\Params\SetClientSecretInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth\Params\SetGrantTypeInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ConstructorInterface;

class GenerateConstructor implements ConstructorInterface, SetClientIdInterface, SetClientSecretInterface, SetGrantTypeInterface
{
    /**
     * @param Constructor   $constructor
     * @param Configuration $configuration
     */
    public function __construct(
        private Constructor $constructor,
        private Configuration $configuration
    ) {
    }

    /**
     * Collecting a constructed body
     *
     * @return void
     */
    public function build(): void
    {
        $this->constructor->build($this);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->constructor->getBody();
    }

    public function setClientId(): void
    {
        $this->constructor->addParam(
            self::PARAM_CLIENT_ID,
            $this->configuration->getAppKey()
        );
    }

    public function setClientSecret(): void
    {
        $this->constructor->addParam(
            self::PARAM_CLIENT_SECRET,
            $this->configuration->getSecretKey()
        );
    }

    public function setGrantType(): void
    {
        $this->constructor->addParam(
            self::PARAM_GRANT_TYPE,
            'client_credentials'
        );
    }
}
