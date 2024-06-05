<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ItemsList\Model;

/**
 * Theme tweaker templates items list
 */
class Template extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_templates/style.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_templates/controller.js';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'template' => [
                static::COLUMN_NAME    => static::t('Template'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_LINK    => 'theme_tweaker_template',
                static::COLUMN_ORDERBY => 100,
            ],
            'date' => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => 'modules/XC/ThemeTweaker/theme_tweaker_templates/parts/cell.date.twig',
                static::COLUMN_NO_WRAP  => false,
                static::COLUMN_ORDERBY  => 200,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XC\ThemeTweaker\Model\Template';
    }

    // {{{ Behaviors

    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' theme_tweaker_templates';
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Zone::P_ORDER_BY} = ['t.date', 'DESC'];

        return $result;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('itemslist.admin.template.blank');
    }
}
