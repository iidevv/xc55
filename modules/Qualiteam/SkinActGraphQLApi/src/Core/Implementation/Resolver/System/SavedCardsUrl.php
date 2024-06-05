<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;
use Qualiteam\SkinActGraphQLApi\Core\UrlHelper;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Config;
use XLite\Core\Converter;

/**
 * Class Languages
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class SavedCardsUrl implements ResolverInterface
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
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
        $cart = $this->cartService->retrieveCart($context);

        $token = $cart?->getApiCartUniqueId();

        return UrlHelper::insertWebAuth(Converter::buildFullURL(
            'XpaymentsCardsFrame',
            '',
            [
                '_token' => $token,
                'shopKey' => Config::getInstance()->Internal->shop_key,
            ],
            \XLite::getCustomerScript(),
            false
        ));
    }
}
