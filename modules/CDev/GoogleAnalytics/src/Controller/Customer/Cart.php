<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XLite\Model\OrderItem;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\Action\Base\AAction;

/**
 * Class Cart
 *
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * Returns event data
     *
     * @param OrderItem $item
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function assembleProductAddedToCartEvent($item)
    {
        $eventData = parent::assembleProductAddedToCartEvent($item);

        $product = $item->getObject();

        $action = new Action\DataDriven\AddedProductEventCell(
            $product,
            $this->getGAProductList(),
            $this->getGAPositionInList(),
            $this->getGACoupon($item),
            $this->isSetCurrentAmount() ? $this->getCurrentAmount() : null,
            $item
        );

        if ($actionData = $action->getActionData(AAction::RETURN_PART_DATA)) {
            $eventData['gaProductData'] = $actionData;
        }

        return $eventData;
    }

    protected function getGAProductList(): string
    {
        return Request::getInstance()->ga_list ?: '';
    }

    protected function getGAPositionInList(): int
    {
        return 0;
    }

    protected function getGACoupon($item): string
    {
        return '';
    }
}
