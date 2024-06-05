<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\SubIriConverter;

use Symfony\Component\Routing\RouterInterface;
use CDev\FeaturedProducts\Model\FeaturedProduct;
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
        return $item instanceof FeaturedProduct;
    }

    /**
     * @param FeaturedProduct $item
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            'api_featured products_get_item',
            [
                'category_id' => $item->getCategory()->getCategoryId(),
                'product_id'  => $item->getProduct()->getProductId(),
            ],
            $referenceType
        );
    }
}
