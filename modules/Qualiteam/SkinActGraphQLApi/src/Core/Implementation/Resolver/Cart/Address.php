<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XcartGraphqlApi\Types\Enum\AddressTypeEnumType;

class Address implements ResolverInterface
{
    /**
     * @var Mapper\Address
     */
    private $mapper;

    /**
     * @var string
     */
    private $type;

    /**
     * Product constructor.
     *
     * @param Mapper\Address $mapper
     * @param string         $type
     */
    public function __construct(Mapper\Address $mapper, $type)
    {
        $this->mapper = $mapper;
        $this->type = $type;
    }

    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed|null
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = $val->cartModel;

        $address = $this->findAddressInProfile($cart->getProfile(), $this->type);

        $result = null;
        if ($address) {
            $result = $this->mapper->mapToDto($address);
            $result['type'] = $this->type;
        }

        return $result;
    }

    /**
     * @param Profile $profile
     * @param string $type
     *
     * @return \XLite\Model\Address
     */
    protected function findAddressInProfile($profile, $type)
    {
        $address = null;

        if ($profile) {
            switch ($type) {
                case AddressTypeEnumType::SHIPPING_TYPE:
                    $address = $profile->getShippingAddress();
                    break;
                case AddressTypeEnumType::BILLING_TYPE:
                    $address = $profile->getBillingAddress();
                    break;
            }
        }

        return $address;
    }
}
