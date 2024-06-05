<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XcartGraphqlApi\ResolverFactoryInterface;

/**
 * Class ObjectType
 * @package XcartGraphqlApi\Types
 */
abstract class ObjectType extends \GraphQL\Type\Definition\ObjectType
{
    /**
     * @var ResolverFactoryInterface
     */
    private $factory;

    /**
     * @return mixed
     */
    abstract protected function configure();

    /**
     * ObjectType constructor.
     *
     * @param ResolverFactoryInterface $factory
     */
    public function __construct(ResolverFactoryInterface $factory)
    {
        $this->factory = $factory;

        $config = $this->configure();

        if (!isset($config['resolveField'])) {
            $config['resolveField'] = array($this, 'resolveField');
        }

        parent::__construct($config);
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function resolveField($value, $args, $context, ResolveInfo $info)
    {
        $method = 'resolve' . ucfirst($info->fieldName);

        $result = null;

        if (method_exists($this, $method)) {
            $result = $this->{$method}($value, $args, $context, $info);
        } elseif (is_array($value)) {
            // TODO Now this default field resolver allows resolver to returns arrays and DTOs, should be only DTO one day
            $result = $value[$info->fieldName];
        } elseif (is_object($value)) {
            // DTO should have all fields public
            $result = $value->{$info->fieldName};
        }

        if ($result instanceof \Closure
            || $result instanceof ResolverInterface
        ) {
            $result = $result($value, $args, $context, $info);
        }

        return $result;
    }

    /**
     * @return ResolverFactoryInterface
     */
    protected function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param $typeName
     *
     * @return \XcartGraphqlApi\Resolver\ResolverInterface
     */
    protected function createResolveForType($typeName)
    {
        $factory = $this->getFactory();

        return $factory->createForType($typeName);
    }
}
