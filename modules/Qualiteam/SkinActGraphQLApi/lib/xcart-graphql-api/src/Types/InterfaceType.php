<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\DTO\CategoryDTO;
use XcartGraphqlApi\DTO\ProductDTO;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XcartGraphqlApi\ResolverFactoryInterface;
use XcartGraphqlApi\Types;

/**
 * Class ObjectType
 * @package XcartGraphqlApi\Types
 */
abstract class InterfaceType extends \GraphQL\Type\Definition\InterfaceType
{
    /**
     * @return mixed
     */
    abstract protected function configure();

    /**
     * ObjectType constructor.
     */
    public function __construct()
    {
        $config = $this->configure();

        if (!isset($config['resolveType'])) {
            $config['resolveType'] = array($this, 'resolveType');
        }

        parent::__construct($config);
    }

    /**
     * @param             $value
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return ObjectType
     * @throws \Exception
     */
    public function resolveType($value, $context, ResolveInfo $info)
    {
        if ($value instanceof ProductDTO) {
            return Types::byName('product');
        }

        if ($value instanceof CategoryDTO) {
            return Types::byName('category');
        }

        return null;
    }
}
