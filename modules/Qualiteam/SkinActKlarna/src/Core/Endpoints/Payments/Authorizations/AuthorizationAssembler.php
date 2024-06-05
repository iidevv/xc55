<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations;

class AuthorizationAssembler
{
    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations\AuthorizationConstructor $authorizationConstructor
     */
    public function __construct(
        private AuthorizationConstructor $authorizationConstructor
    )
    {
        $this->authorizationConstructor->build();
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->authorizationConstructor->getBody();
    }
}