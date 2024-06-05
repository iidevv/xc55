<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations;

use Qualiteam\SkinActKlarna\Core\Endpoints\Constructor;
use Qualiteam\SkinActKlarna\Helpers\Profile as ProfileHelper;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params\SetBillingAddressInterface;

class AuthorizationConstructor implements SetBillingAddressInterface
{

    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Constructor $constructor
     * @param \Qualiteam\SkinActKlarna\Helpers\Profile            $profileHelper
     */
    public function __construct(
        private Constructor   $constructor,
        private ProfileHelper $profileHelper,
    ) {
    }

    /**
     * Set billing address
     *
     * @return void
     */
    public function setBillingAddress(): void
    {
        $this->constructor->addParam(
            static::PARAM_BILLING_ADDRESS,
            $this->profileHelper->getBillingAddress()
        );
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

    public function getBody(): array
    {
        return $this->constructor->getBody();
    }
}