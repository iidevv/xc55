<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Migration\Action;

use XCart\Extender\Mapping\ListChild;

/**
 * Complete action
 *
 * @ListChild (list="migration_wizard.actions", zone="admin")
 */
class Complete extends \XC\MigrationWizard\View\Migration\Action\AAction
{
    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'complete.twig';
    }

    protected function hasTransferDataErrors()
    {
        return \XC\MigrationWizard\Logic\Migration\Wizard::hasTransferDataErrors();
    }

    protected function isDemoMode()
    {
        return \XLite::getController()->getWizard()->isDemoMode();
    }

    protected function hasUsersRule()
    {
        $result = false;

        $transferDataStep = \XLite::getController()->getWizard()->getStep('TransferData');
        if ($transferDataStep) {
            $result = $transferDataStep->hasRule('XC\MigrationWizard\Logic\Import\Processor\XCart\Users');
        }

        return $result;
    }

    protected function getDemoCategoryUrl()
    {
        $categoryId = \XLite::getController()->getWizard()->getDemoCategoryId();

        $result = '';
        if ($categoryId) {
            $params = ['category_id' => $categoryId];
            if (\XLite\Core\Auth::getInstance()->isClosedStorefront()) {
                $params['shopKey'] = \XLite\Core\Auth::getInstance()->getShopKey();
            }

            $result = \XLite::getController()->getShopURL(\XLite\Core\Converter::buildURL('category', '', [], \XLite::getInstance()->getCustomerScript()), null, $params);
        }

        return $result;
    }
}
