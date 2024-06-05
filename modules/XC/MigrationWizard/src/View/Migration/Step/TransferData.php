<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Migration\Step;

use XCart\Extender\Mapping\ListChild;

/**
 * Transfer data step
 *
 * @ListChild (list="migration_wizard.sections", zone="admin")
 */
class TransferData extends \XC\MigrationWizard\View\Migration\Step\AStep
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
}
