<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\FormField\Select;

/**
 * Pages selector
 */
class Pages extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Get Memberships list
     *
     * @return array
     */
    protected function getPagesList()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('\CDev\SimpleCMS\Model\Page')->findActivePages() as $p) {
            $list[$p->getId()] = $p->getName();
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
        return $this->getPagesList();
    }
}
