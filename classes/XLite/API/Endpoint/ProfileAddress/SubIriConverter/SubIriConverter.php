<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\SubIriConverter;

use Symfony\Component\Routing\RouterInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;
use XLite\Model\Address;

class SubIriConverter implements SubIriFromItemConverterInterface
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supportIriFromItem(object $item, int $referenceType): bool
    {
        return $item instanceof Address;
    }

    /**
     * @param Address $item
     * @inheritDoc
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            'api_addresses_get_item',
            [
                'address_id' => $item->getAddressId(),
                'profile_id' => $item->getProfile()->getProfileId(),
            ],
            $referenceType
        );
    }
}
