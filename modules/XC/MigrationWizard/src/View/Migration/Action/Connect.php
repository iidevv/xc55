<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Migration\Action;

use XCart\Extender\Mapping\ListChild;

/**
 * Connect action
 *
 * @ListChild (list="migration_wizard.actions", zone="admin")
 */
class Connect extends \XC\MigrationWizard\View\Migration\Action\AAction
{
    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'connect.twig';
    }
}
