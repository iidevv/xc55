<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\API\Endpoint\ProductUpsellingProduct\SubIriConverter;

use XC\Upselling\Model\UpsellingProduct as Model;
use Symfony\Component\Routing\RouterInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;

class SubIriConverter implements SubIriFromItemConverterInterface
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supportIriFromItem(object $item, int $referenceType): bool
    {
        return $item instanceof Model;
    }

    /**
     * @param Model $item
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            'api_related products_get_item',
            [
                'product_id' => $item->getProduct()->getProductId(),
                'id'         => $item->getId(),
            ],
            $referenceType
        );
    }
}
