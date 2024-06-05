<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\Model\Product;

/**
 * GlobalTab
 */
class GlobalTab extends \XC\CustomProductTabs\View\Model\GlobalTab
{
    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return (int)\XLite\Core\Request::getInstance()->global_tab_id;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XC\CustomProductTabs\View\Form\Model\Product\GlobalTab';
    }
}
