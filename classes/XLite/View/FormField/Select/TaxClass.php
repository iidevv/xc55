<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Tax class selector
 */
class TaxClass extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get tax classes list
     *
     * @return array
     */
    protected function getTaxClassesList()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\TaxClass')->findAll() as $e) {
            $list[$e->getId()] = $e->getName();
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
        return [
            '0' => static::t('Default tax class'),
        ] + $this->getTaxClassesList();
    }
}
