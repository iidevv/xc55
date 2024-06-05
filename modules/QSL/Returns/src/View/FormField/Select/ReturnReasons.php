<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\FormField\Select;

/**
 * Return reasons selector
 */
class ReturnReasons extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get return reasons list
     *
     * @return array
     */
    protected function getReturnReasonsList()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('\QSL\Returns\Model\ReturnReason')->findBy([], ['position' => 'ASC']) as $reason) {
            $list[$reason->getId()] = $reason->getReasonName();
        }

        if (!\XLite\Core\Config::getInstance()->QSL->Returns->hide_other_reason) {
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
        return $this->getReturnReasonsList();
    }
}
