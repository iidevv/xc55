<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View;

use XCart\Extender\Mapping\Extender;

/**
 * Migration Wizard view
 *
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    public const MIGRATION_WIZARD_MODULE_PATH = 'modules/XC/MigrationWizard';
}
