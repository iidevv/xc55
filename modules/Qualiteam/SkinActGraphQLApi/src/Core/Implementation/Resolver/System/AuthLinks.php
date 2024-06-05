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

/**
 * Class AppData
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class AuthLinks implements ResolverInterface
{
    /**
     * @return string
     */
    protected function buildRegistration()
    {
        return \XLite\Core\Converter::buildFullURL(
            'profile',
            '',
            [],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildPasswordChanges()
    {
        return \XLite\Core\Converter::buildFullURL(
            'recover_password',
            '',
            [],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return [
            'registration'     => $this->buildRegistration(),
            'password_change'  => $this->buildPasswordChanges(),
        ];
    }
}
