<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\Product;

use XCart\Extender\Mapping\ListChild;

/**
 * Product buttons separator
 *
 * @ListChild (list="product.details.page.info.form.buttons.cart-buttons", zone="customer", weight="140")
 * @ListChild (list="product.details.page.info.form.buttons-added.cart-buttons", zone="customer", weight="140")
 */
class ButtonsSeparator extends \CDev\Paypal\View\Button\AButtonsSeparator
{
    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $availableForSale = false;
        $controller = \XLite::getController();
        if ($controller instanceof \XLite\Controller\Customer\Product) {
            $availableForSale = !$controller->getProduct()->isAllStockInCart() && $controller->getProduct()->isAvailable();
        }

        return parent::isVisible()
            && $availableForSale
            && \CDev\Paypal\Main::isBuyNowEnabled();
    }
}
