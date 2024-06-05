<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules;

use GraphQL\Type\Definition\ResolveInfo;
use Includes\Utils\Module\Manager;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\CommonCell;
use XLite\Model\Order;
use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\NoModule;

/**
 * Class MenuNotifications
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules
 */
abstract class MenuNotifications implements ResolverInterface
{
    protected function prepareResult(ContextInterface $context, array $arr)
    {
        return array_merge($this->definitionResult($context), $arr);
    }

    protected function definitionResult(ContextInterface $context)
    {
        $profile = $context->getLoggedProfile();

        return [
            'messages'      => $profile ? $this->getCountOwnUnreadMessages($profile) : 0,
            'orders'        => $profile ? $this->getOrdersCount($profile) : 0
        ];
    }

    protected function getCountOwnUnreadMessages(Profile $profile)
    {
        return static::isModuleEnabled('XC\VendorMessages') ? $profile->countOwnUnreadMessages() : 0;
    }

    protected function getOrdersCount(Profile $profile)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Order::P_RECENT} = 1;

        $this->prepareMultiVendorCnd($cnd, $profile);

        return \XLite\Core\Database::getRepo(Order::class)->searchRecentOrders($cnd, true);
    }

    protected function prepareMultiVendorCnd(CommonCell $cnd, Profile $profile)
    {
        if (static::isModuleEnabled('XC\MultiVendor')) {
            $cnd->{\XLite\Model\Repo\Order::P_VENDOR} = $profile;
        }

        return $cnd;
    }

    protected static function isModuleEnabled($name)
    {
        return Manager::getRegistry()->isModuleEnabled($name);
    }
}