<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi;

use XLite\Controller\AController;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

abstract class XLite extends \XLite
{
    /**
     * Reset controller data for JSON API
     *
     * @return void
     */
    public static function initializeForMobileApi()
    {
        static::$controller = null;
        static::$adminZone = true;
        \XLite\Model\CachingFactory::clearCache();
    }

    /**
     * @return AController
     */
    public static function safeGetController()
    {
        if (null !== static::$controller) {
            return static::getController();
        }

        return null;
    }

    /**
     * @var XCartContext
     */
    protected static $graphQLContext = null;

    /**
     * @param XCartContext $context
     */
    public static function initGQLContext($context)
    {
        static::$graphQLContext = $context;
    }

    /**
     * @return bool
     */
    public static function inGraphQLContext()
    {
        return static::$graphQLContext !== null;
    }

    /**
     * @return XCartContext
     */
    public static function getGraphQLContext()
    {
        return static::$graphQLContext;
    }

    public static function isAdminZone()
    {
        return static::inGraphQLContext()
            ? static::getGraphQLContext()->isAuthenticated() && static::getGraphQLContext()->hasAdminAccess()
            : parent::isAdminZone();
    }
}
