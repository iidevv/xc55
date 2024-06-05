<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Address;

/**
 * Class ProfileFields
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class ProfileFields implements ResolverInterface
{

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
        /** @var \XLite\Model\AddressField[] $fields */
        $fields = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled();

        $mapped = array_map(function($module) {
            return $this->mapToDto($module);
        }, $fields);

        $mapped = array_filter($mapped);

        array_unshift($mapped, $this->getEmailProfileField());

        return $mapped;
    }

    /**
     * @param \XLite\Model\AddressField $model
     *
     * @return array
     */
    protected function mapToDto($model)
    {
        $serviceName = $model->getServiceName();
        $apiName = Address::translateServiceNameForApi($serviceName);

        if ($apiName === 'custom_state') {
            return [];
        }

        return [
            'service_name'  => $apiName,
            'type'          => static::getFieldTypeByName($apiName),
            'name'          => $model->getName(),
            'placeholder'   => '',
            'required'      => $model->getRequired(),
        ];
    }

    protected function getEmailProfileField()
    {
        return [
            'service_name'  => 'email',
            'type'          => static::getFieldTypeByName('email'),
            'name'          => (string)\XLite\Core\Translation::lbl('Email'),
            'placeholder'   => '',
            'required'      => true,
        ];
    }

    /**
     * TODO Is it really needed?
     * TODO Make fieldTypeEnum if it is
     * Get field type by service name
     *
     * @param string $name Service field name
     *
     * @return string
     */
    protected static function getFieldTypeByName($name)
    {
        $type = 'input';

        switch ($name) {
            case 'email':
                $type = 'email';
                break;
            case 'country':
                $type = 'country';
                break;
            case 'state':
                $type = 'state';
                break;
            case 'phone':
                $type = 'phone';
                break;
            case 'fax':
                $type = 'phone';
                break;
        }

        return $type;
    }
}
