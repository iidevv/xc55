<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Event;
use XLite\Model\Product;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\Action\Base\AAction;
use QSL\MyWishlist\Model\Wishlist;

/**
 * Wishlist products page controller
 *
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Define the top message defined by the result flag
     *
     * @param boolean $result Flag of adding procedure result
     * @param Product $product
     *
     * @return void
     * @noinspection PhpMissingParamTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @noinspection PhpUnused
     */
    protected function processWishlistMessage($result, $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::processWishlistMessage($result, $product);

        if ($this->needGAAddToWishlistEvent($result, $product)) {
            $eventData = $this->assembleGAAddToWishlistEvent($result, $product);
            $this->registerGAAddToWishlistEvent($eventData, $product);
        }
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function needGAAddToWishlistEvent($result, $product): bool
    {
        return in_array($result, [Wishlist::FLAG_ADDED, Wishlist::FLAG_ALREADY_ADDED], true);
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function assembleGAAddToWishlistEvent($result, $product): array
    {
        $eventData = [];

        $action = new Action\DataDriven\AddedProductEventCell(
            $product
        );

        if ($actionData = $action->getActionData(AAction::RETURN_PART_DATA)) {
            $eventData['gaProductData'] = $actionData;
        }

        return $eventData;
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function registerGAAddToWishlistEvent($eventData, $product): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        Event::gaProductAddedToWishlist($eventData);
    }
}
