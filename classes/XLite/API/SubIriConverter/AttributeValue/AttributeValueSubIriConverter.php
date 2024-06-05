<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubIriConverter\AttributeValue;

use Symfony\Component\Routing\RouterInterface;
use XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing\SubIriConverter\SubIriFromItemConverterInterface;
use XLite\Model\AttributeValue\AAttributeValue;

class AttributeValueSubIriConverter implements SubIriFromItemConverterInterface
{
    protected RouterInterface $router;

    protected string $routeItemName;

    protected string $className;

    public function __construct(RouterInterface $router, string $routeItemName, string $className)
    {
        $this->router = $router;
        $this->routeItemName = $routeItemName;
        $this->className = $className;
    }

    public function supportIriFromItem(object $item, int $referenceType): bool
    {
        return $item instanceof AAttributeValue && get_class($item) === $this->className;
    }

    /**
     * @param AAttributeValue $item
     */
    public function getIriFromItem(object $item, int $referenceType): string
    {
        return $this->router->generate(
            $this->routeItemName,
            [
                'product_id'   => $item->getProduct()->getId(),
                'attribute_id' => $item->getAttribute()->getId(),
                'id'           => $item->getId(),
            ],
            $referenceType
        );
    }
}
