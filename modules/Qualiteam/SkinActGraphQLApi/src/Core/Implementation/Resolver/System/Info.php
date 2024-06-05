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
class Info implements ResolverInterface
{
    /**
     * @return string
     */
    protected function buildContactUS()
    {
        return \XLite\Core\Converter::buildFullURL(
            'page',
            '',
            ['id' => 27],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildShipping()
    {
        return \XLite\Core\Converter::buildFullURL(
            'page',
            '',
            ['id' => 2],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildTerms()
    {
        return \XLite\Core\Converter::buildFullURL(
            'page',
            '',
            ['id' => 1],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildPrivacy()
    {
        return \XLite\Core\Converter::buildFullURL(
            'page',
            '',
            ['id' => 15],
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
        $return = [
            'shipping'                  => $this->buildShipping(),
            'contacts'                  => $this->buildContactUS(),
            'terms_and_conditions'      => $this->buildTerms(),
            'privacy_policy'            => $this->buildPrivacy(),
        ];

        return $return;
    }
}
