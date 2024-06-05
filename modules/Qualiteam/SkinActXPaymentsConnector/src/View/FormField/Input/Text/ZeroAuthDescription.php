<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\FormField\Input\Text;

use XLite\Core\Translation;
use XLite\View\FormField\Input\Text;

/**
 * Zero-dollar auth description
 *
 */
class ZeroAuthDescription extends Text
{
    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel()
    {
        return Translation::lbl('Description of the card setup payment');
    }

    /**
     * Get default name
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return 'description';
    }

}
