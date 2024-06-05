<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\Export;

use XCart\Extender\Mapping\Extender;

/**
 * Completed section
 * @Extender\Mixin
 */
class CompletedPopup extends \XLite\View\Export\CompletedPopup
{
    /**
     * Get message which is shown after export
     *
     * @return string
     */
    protected function getCompleteMessage()
    {
        $message = parent::getCompleteMessage();

        if ($this->isExportHasNFO()) {
            $message = static::t('Not Finished orders were skipped during the export process. If you wish to export the orders which are now in this state, change their fulfillment status from Not Finished to any other.');
        }

        return $message;
    }
}
