<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $labels = parent::getLabels();

        $labels += \CDev\ProductAdvisor\Main::getProductPageLabels($this->getProduct());

        return $labels;
    }

    /**
     * Return coming soon label
     *
     * @return array
     */
    protected function getComingSoonLabel()
    {
        return [
            \CDev\ProductAdvisor\Main::PA_MODULE_PRODUCT_LABEL_SOON => \XLite\Core\Translation::getInstance()->translate(
                'Expected on X',
                ['date' => \XLite\Core\Converter::getInstance()->formatDate($this->getProduct()->getArrivalDate())]
            )
        ];
    }

    /**
     * @return bool
     */
    protected function isShowComingSoonLabel()
    {
        return $this->getProduct()->isUpcomingProduct()
            && \CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isProductPageEnabled(
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_mark_with_label
            );
    }
}
