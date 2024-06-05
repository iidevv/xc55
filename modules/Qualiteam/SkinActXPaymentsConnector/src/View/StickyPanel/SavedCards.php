<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\StickyPanel;

use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;
use XLite\View\StickyPanel\ItemsListForm;

/**
 * Saved cards list buttons (sticky panel) 
 */
class SavedCards extends ItemsListForm
{
    /**
     * Check panel has more actions buttons
     *
     * @return boolean
     */
    protected function hasMoreActionsButtons()
    {
        return ZeroAuth::getInstance()->allowZeroAuth();
    }
}
