<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeProperty\SubIriConverter;

use Symfony\Component\Routing\RouterInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;
use XLite\Model\AttributeProperty;

class AttributePropertySubIriConverter implements SubIriFromItemConverterInterface
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supportIriFromItem(object $item, int $referenceType): bool
    {
        return $item instanceof AttributeProperty;
    }

    /**
     * @param AttributeProperty $item
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            'api_attribute properties_post_collection',
            [
                'product_id'   => $item->getProduct()->getId(),
                'attribute_id' => $item->getAttribute()->getId(),
                'id'           => $item->getId(),
            ],
            $referenceType
        );
    }
}
