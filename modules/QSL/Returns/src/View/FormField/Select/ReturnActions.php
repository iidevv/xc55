<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\FormField\Select;

/**
 * Return actions selector
 */
class ReturnActions extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get return actions list
     *
     * @return array
     */
    protected function getReturnActionsList()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('\QSL\Returns\Model\ReturnAction')->findBy([], ['position' => 'ASC']) as $action) {
            $list[$action->getId()] = $action->getActionName();
        }

        if (!\XLite\Core\Config::getInstance()->QSL->Returns->hide_other_action) {
            $list[0] = static::t('Other');
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getReturnActionsList();
    }
}
