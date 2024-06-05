<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductImage\SubIriConverter;

use Symfony\Component\Routing\RouterInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;
use XLite\Model\Image\Product\Image;

class SubIriConverter implements SubIriFromItemConverterInterface
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supportIriFromItem(object $item, int $referenceType): bool
    {
        return $item instanceof Image;
    }

    /**
     * @param Image $item
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            'api_product images_get_item',
            [
                'product_id' => $item->getProduct()->getProductId(),
                'image_id'   => $item->getId(),
            ],
            $referenceType
        );
    }
}
