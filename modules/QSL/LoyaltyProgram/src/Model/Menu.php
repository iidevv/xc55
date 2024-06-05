<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Menu
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Menu extends \CDev\SimpleCMS\Model\Menu
{
    public const DEFAULT_LOYALTY_PROGRAM_PAGE = '{loyalty program}';

    /**
     * Defines the resulting link values for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkURLs()
    {
        $list = parent::defineLinkURLs();

        $list[static::DEFAULT_LOYALTY_PROGRAM_PAGE] = '?target=loyalty_program_details';

        return $list;
    }

    /**
     * Defines the link controller class names for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkControllers()
    {
        $list = parent::defineLinkControllers();

        $list[static::DEFAULT_LOYALTY_PROGRAM_PAGE] = '\QSL\LoyaltyProgram\Controller\Customer\LoyaltyProgramDetails';

        return $list;
    }
}
