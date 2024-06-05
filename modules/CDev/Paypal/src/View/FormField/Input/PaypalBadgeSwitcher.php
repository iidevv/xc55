<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Input;

class PaypalBadgeSwitcher extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/Paypal/form_field/paypal_badge_switcher.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'modules/CDev/Paypal/images/paypal_accept.svg',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite::INTERFACE_WEB,
            \XLite::ZONE_ADMIN
        );
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/paypal_badge_switcher.less';

        return $list;
    }
}
