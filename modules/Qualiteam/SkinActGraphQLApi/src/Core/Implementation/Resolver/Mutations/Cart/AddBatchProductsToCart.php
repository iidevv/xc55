<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\Cart;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CartServiceException;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

class AddBatchProductsToCart implements ResolverInterface
{
    /**
     * @var Mapper\Cart
     */
    private $mapper;
    /**
     * @var CartService
     */
    private $cartService;

    /**
     * Product constructor.
     *
     * @param Mapper\Cart $mapper
     */
    public function __construct(Mapper\Cart $mapper, CartService $cartService)
    {
        $this->mapper = $mapper;
        $this->cartService = $cartService;
    }

    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo $info
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $cart = $this->cartService->retrieveCart($context);

        if (!empty($args['productsLines'])) {

            foreach ($args['productsLines'] as $productLine) {

                $product = $this->getProduct($productLine['id']);

                if (!$product) {
                    throw new CartServiceException("Product {$productLine['id']} not found");
                }

                $amount = $productLine['amount'] ?? 1;

                $attributes = $productLine['attributes'] ?? [];

                if ($product->hasEditableAttributes() && empty($attributes)) {
                    $attributes = $this->getDefaultAttributesFor($product);
                }

                $attributes = $this->prepareAttributes($attributes);

                $this->cartService->addItem($cart, $product, $amount, $attributes);
            }
        }

        return $this->mapper->mapToDto(
            $cart
        );
    }

    /**
     * @param $product_id
     *
     * @return \XLite\Model\Product|\XLite\Model\AEntity
     */
    protected function getProduct($product_id)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->find($product_id);
    }

    /**
     * @param $attributes
     *
     * @return array
     */
    protected function prepareAttributes($attributes)
    {
        $mapped = [];

        foreach ($attributes as $attrStr) {
            $delimiterPos = strpos($attrStr, ":");

            if ($delimiterPos !== false) {
                $parts = [
                    substr($attrStr, 0, $delimiterPos),
                    substr($attrStr, $delimiterPos + 1)
                ];

                if ($parts[0] && $parts[1]) {
                    $mapped[$parts[0]] = $parts[1];
                }
            }
        }

        return $mapped;
    }

    protected function getDefaultAttributesFor(\XLite\Model\Product $product)
    {
        $attrs = $product->getEditableAttributes();

        $defaultAttributes = [];

        /** @var \XLite\Model\Attribute $attr */
        foreach ($attrs as $attr) {
            $values = $attr->getAttributeValues();
            if ($values) {
                $value = current($values);
                $defaultAttributes[] = $attr->getId() . ':' . $value->getId();
            }
        }

        return $defaultAttributes;
    }
}
