<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Menus repository
 *
 * @see https://bt.x-cart.com/view.php?id=46721
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
abstract class Menu extends \CDev\SimpleCMS\Model\Repo\Menu
{
    /**
     * Get translation repository
     *
     * @return \XLite\Model\repo\ARepo
     */
    public function getTranslationRepository()
    {
        return \XLite\Core\Database::getRepo($this->_entityName . 'Translation');
    }
}
