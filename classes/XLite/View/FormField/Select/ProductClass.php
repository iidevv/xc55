<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Product class selector
 */
class ProductClass extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get product classes list
     *
     * @return array
     */
    protected function getProductClassesList()
    {
        $list = [];
        $cnd = new \XLite\Core\CommonCell();
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\ProductClass')->search($cnd) as $e) {
            $list[$e->getId()] = htmlspecialchars($e->getName());
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
        return [0 => static::t('No class')] + $this->getProductClassesList();
    }
}
