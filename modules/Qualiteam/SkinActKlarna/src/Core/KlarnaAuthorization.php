<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core;

use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations\AuthorizationAssembler;

class KlarnaAuthorization
{
    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations\AuthorizationAssembler $authorizationAssembler
     */
    public function __construct(
        private AuthorizationAssembler $authorizationAssembler
    )
    {
    }

    /**
     * @return array
     */
    protected function collectAuthorizationParams(): array
    {
        return $this->authorizationAssembler->getBody();
    }

    /**
     * @return array
     */
    public function getAuthorizationParams(): array
    {
        return $this->collectAuthorizationParams();
    }
}