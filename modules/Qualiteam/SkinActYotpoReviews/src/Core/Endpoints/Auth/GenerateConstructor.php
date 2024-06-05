<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ConstructorInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetSecretInterface;

class GenerateConstructor implements ConstructorInterface, SetSecretInterface
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor       $constructor
     * @param \Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration $configuration
     */
    public function __construct(
        private Constructor   $constructor,
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

    /**
     * @return void
     */
    public function setSecret(): void
    {
        $this->constructor->addParam(
            self::PARAM_SECRET,
            $this->configuration->getSecretKey()
        );
    }
}