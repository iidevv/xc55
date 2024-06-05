<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Migration\Action;

use XCart\Extender\Mapping\ListChild;

/**
 * Transfer data action
 *
 * @ListChild (list="migration_wizard.actions", zone="admin")
 */
class TransferData extends \XC\MigrationWizard\View\Migration\Action\AAction
{
    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'transfer_data.twig';
    }

    protected function getCurrencyAlertText()
    {
        $currency = \XLite::getInstance()->getCurrency();
        $currencyStr = sprintf('%s - %s', $currency->getCode(), $currency->getName());
        $url = $this->buildURL('currency');

        return static::t('The orders will be migrated using the current store currency (X).', ['currency' => $currencyStr, 'url' => $url]);
    }
}
