<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\OAuth2Client\Mapper;

class Provider
{

    /**
     * @param \QSL\OAuth2Client\Model\Provider $provider
     *
     * @return array
     */
    public function mapToArray(\QSL\OAuth2Client\Model\Provider $provider)
    {
        $returnUrl = \XLite::getController()->getShopURL('');
        return [
            'display_name' => $provider->getName(),
            'service_name' => $provider->getServiceName(),
            'authorize_url' => $provider->getWrapper()->getRequestURL($returnUrl)
        ];
    }
}