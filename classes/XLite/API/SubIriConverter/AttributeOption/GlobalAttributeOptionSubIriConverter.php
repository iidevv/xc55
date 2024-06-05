<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubIriConverter\AttributeOption;

use Symfony\Component\Routing\RouterInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;
use XLite\Model\AttributeOption;

class GlobalAttributeOptionSubIriConverter implements SubIriFromItemConverterInterface
{
    protected RouterInterface $router;

    protected string $attributeType;

    protected string $itemRouteName;

    public function __construct(RouterInterface $router, string $attributeType, string $itemRouteName)
    {
        $this->router        = $router;
        $this->attributeType = $attributeType;
        $this->itemRouteName = $itemRouteName;
    }

    public function supportIriFromItem(object $item, int $referenceType): bool
    {
        return $item instanceof AttributeOption
            && $item->getAttribute()->getType() === $this->attributeType
            && $item->getAttribute()->getProductClass() === null
            && $item->getAttribute()->getProduct() === null;
    }

    /**
     * @param AttributeOption $item
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            $this->itemRouteName,
            [
                'attribute_id' => $item->getAttribute()->getId(),
                'id'           => $item->getId(),
            ],
            $referenceType
        );
    }
}
