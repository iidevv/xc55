<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\ListChild;

/**
 * File Selector Dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class SaleSelectedDialog extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'sale_selected';

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Set the sale price';
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'modules/CDev/Sale/sale_selected_dialog/body.twig';
    }
}
