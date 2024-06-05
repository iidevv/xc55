<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\FormField\Input\Checkbox;

class UseXpCloudForSubscriptions extends \XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel
{
    /**
     * Get XPay\XPaymentsCloud module ID
     *
     * @return integer
     */
    public function getModuleID()
    {
        return \Includes\Utils\Module\Module::buildId('XPay', 'XPaymentsCloud');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $request = \XLite\Core\Request::getInstance();

        if (
            'module' == $request->target 
            && $this->getModuleID() == $request->moduleId
        ) {

            $result = false;

        } else {

            $result = parent::isVisible();
        }

        return $result;
    }
}
