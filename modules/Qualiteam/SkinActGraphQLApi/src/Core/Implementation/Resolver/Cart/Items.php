<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

class Items implements ResolverInterface
{
    /**
     * @var Mapper\Product
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\Product $mapper
     */
    public function __construct(Mapper\Product $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $items = $val->items;

        if (!$items) {
            return [];
        }

        if ($items instanceof Collection) {
            $items = $items->toArray();
        }

        return array_map(
            function($item) {
                return $this->mapItem($item);
            },
            $items
        );
    }

    protected function mapItem(\XLite\Model\OrderItem $item)
    {
        return [
            'id'              => $item->getItemId(),
            'product'         => function () use ($item) {
                return $this->mapper->mapToDto(
                    $item->getProduct()
                );
            },
            'sku'             => $item->getSku(),
            'name'            => $item->getName(),
            'price'           => $this->roundPriceForCart($item->getOrder(), $item->getItemNetPrice()),
            'amount'          => $item->getAmount(),
            'total'           => $item->getTotal(),
            'options'         => $this->mapOptions($item),
            // TODO Stubs
            'taxes'           => [],
            'is_booking'      => false,
            'date_from'       => '',
            'date_to'         => '',
        ];
    }

    /**
     * @param \XLite\Model\Order $cart
     * @param                   $value
     *
     * @return float
     */
    protected function roundPriceForCart(\XLite\Model\Order $cart, $value)
    {
        return $cart->getCurrency()
            ? $cart->getCurrency()->roundValue($value)
            : $value;
    }

    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    protected function mapOptions(\XLite\Model\OrderItem $item)
    {
        $options = [];

        foreach ($item->getAttributeValues() as $attrValue) {
            $options[] = [
                'id'    => $attrValue->getId(),
                'name'  => $attrValue->getName(),
                'value' => $attrValue->getActualValue(),
                'option_id' => $attrValue->getAttributeId(),
                'option_value_id' => $attrValue->getAttributeValueId(),
            ];
        }

        return $options;
    }
}
