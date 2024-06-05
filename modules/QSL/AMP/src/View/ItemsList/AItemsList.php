<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\ItemsList;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AItemsList extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "getBody()" and "get[CSS/JS]Files()")
     *
     * @return string
     */
    protected function getDir()
    {
        return static::isAMP() ? 'modules/QSL/AMP/items_list' : parent::getDir();
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return static::isAMP() ? 'modules/QSL/AMP/items_list/body.twig' : parent::getBody();
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return static::isAMP() ? 'amp.' . parent::getListName() : parent::getListName();
    }

    /**
     * getURLParams
     *
     * @return array
     */
    protected function getURLParams()
    {
        $params = parent::getURLParams();

        if (static::isAMP()) {
            unset($params[self::PARAM_SESSION_CELL]);
        }

        return $params;
    }
}
