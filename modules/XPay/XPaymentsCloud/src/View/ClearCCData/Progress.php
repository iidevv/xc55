<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\ClearCCData;

use XPay\XPaymentsCloud\Logic\ClearCCData\Generator;
use XLite\View\AView;
use XLite\View\EventTaskProgressProviderTrait;

/**
 * Clear credit card data progress section
 */
class Progress extends AView
{
    use EventTaskProgressProviderTrait;

    protected function getDir()
    {
        return 'modules/XPay/XPaymentsCloud/clear_cc_data/';
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . 'style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . 'controller.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'progress.twig';
    }

    /**
     * @inheritdoc
     */
    protected function getProcessor()
    {
        return Generator::getInstance();
    }

}
