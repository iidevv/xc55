<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Form\UserSelection;

use XLite\Core\Request;

/**
 * Search profiles form
 */
class Search extends \XLite\View\Form\Profiles\AProfiles
{

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'user_selection';
    }

    protected function getFormParams()
    {
        $params = parent::getFormParams();

        if (Request::getInstance()->order_number > 0) {
            $params['order_number'] = (int)Request::getInstance()->order_number;
        }

        return $params;
    }
}
