<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Reminder Selector Dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class RemindSelectedDialog extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'remind_selected_carts';

        return $list;
    }

    /**
     * Return form return url.
     *
     * @return string
     */
    protected function getReturnUrl()
    {
        return (string)\XLite\Core\Request::getInstance()->returnUrl;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'modules/QSL/AbandonedCartReminder/remind_dialog/body.twig';
    }
}
