<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\SubIriConverter;

use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice as Model;
use Symfony\Component\Routing\RouterInterface;
use XCart\Extender\Mapping\Extender;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;

/**
 * @Extender\Depend("XC\ProductVariants")
 */
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
            'api_product variant wholesale prices_get_item',
            [
                'product_id' => $item->getProductVariant()->getProduct()->getProductId(),
                'variant_id' => $item->getProductVariant()->getId(),
                'id'         => $item->getId(),
            ],
            $referenceType
        );
    }
}
