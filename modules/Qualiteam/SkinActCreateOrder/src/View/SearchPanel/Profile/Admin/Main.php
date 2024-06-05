<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\SearchPanel\Profile\Admin;

/**
 * Main admin profile search panel
 */
class Main extends \XLite\View\SearchPanel\Profile\Admin\Main
{

    protected function isUseFilter()
    {
        return false;
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        $conditions = parent::defineConditions();

        unset($conditions['membership']);
        unset($conditions['user_type']);

        return $conditions;
    }

    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        $conditions = parent::defineHiddenConditions();

        unset($conditions['date_type']);

        return $conditions;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\Form\UserSelection\Search';
    }

    /**
     * Get container form class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' user-selection-search';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActCreateOrder/user_selection_style.css';

        return $list;
    }
}
