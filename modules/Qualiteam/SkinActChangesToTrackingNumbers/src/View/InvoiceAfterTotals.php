<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\View;

use XLite\Core\Request;
use XLite\View\AView;
use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="invoice.base.totals.after", weight="100")
 */
class InvoiceAfterTotals extends AView
{
    protected function isVisible()
    {
        // do not include in pdf
        return \XLite\Core\Layout::getInstance()->getInterface() === \XLite::INTERFACE_WEB
            && Request::getInstance()->mode === null
            && !empty($this->getTrackingNumbers());
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActChangesToTrackingNumbers/invoice_after_totals.twig';
    }

    protected function getTrackingNumbers()
    {
        $numbers = $this->getOrder()->getTrackingNumbers();

        $result = [];
        foreach ($numbers as $number) {
            $result[] = $number->getValue();
        }

        return $result;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/invoice_after_totals.css';
        return $list;
    }
}