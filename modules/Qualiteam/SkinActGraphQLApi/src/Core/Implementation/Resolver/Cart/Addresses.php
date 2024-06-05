<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XcartGraphqlApi\Types\Enum\AddressTypeEnumType;

class Addresses implements ResolverInterface
{
    /**
     * @var Mapper\Address
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\Address $mapper
     */
    public function __construct(Mapper\Address $mapper)
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
        $addresses = $val->address_list;

        if (isset($addresses['S'])) {
            $addresses['S'] = $this->mapper->mapToDto($addresses['S']);
            $addresses['S']['type'] = AddressTypeEnumType::SHIPPING_TYPE;
        }

        if (isset($addresses['B'])) {
            $addresses['B'] = $this->mapper->mapToDto($addresses['B']);
            $addresses['B']['type'] = AddressTypeEnumType::BILLING_TYPE;
        }

        return $addresses;
    }
}
