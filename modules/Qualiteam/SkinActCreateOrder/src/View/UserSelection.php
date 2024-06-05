<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Product selections page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class UserSelection extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['user_selection']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCreateOrder/user_selection/body.twig';
    }

    /**
     * Return widget body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'modules/Qualiteam/SkinActCreateOrder/user_selection/list.twig';
    }

    /**
     * Returns widget inner items list class
     * 
     * @return string
     */
    protected function getItemsListClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\ItemsList\Model\ProfileSelect';
    }

}