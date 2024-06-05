<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Model\WidgetParam\TypeString;
use XLite\View\FormField\Input\AInput;

class TreeSelect extends AInput
{
    public const PARAM_THEME = 'theme';
    public const PARAM_LIST  = 'list';

    public const PARAM_SELECT_ALL  = 'select_all';

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'jstree/jstree.js',
                'form_field/tree_select/tree_select.js',
            ]
        );
    }

    protected function prepareAttributes(array $attrs)
    {
        $list = parent::prepareAttributes($attrs);

        $list['type'] = 'hidden';
        $list['class'] = 'tree-select-input';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                "jstree/themes/{$this->getParam('theme')}/style.css"
            ]
        );
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_THEME       => new TypeString('Theme', $this->getDefaultTheme()),
            static::PARAM_LIST        => new TypeString('List', $this->getDefaultList()),
            static::PARAM_SELECT_ALL  => new \XLite\Model\WidgetParam\TypeBool('Select/Unselect All', false),
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultTheme()
    {
        return 'xcart';
    }

    /**
     * @return array
     */
    protected function getDefaultList()
    {
        return [];
    }

    public function getPluginsListConfig(): array
    {
        return ['wholerow', 'checkbox'];
    }

    public function getThemesConfig(): array
    {
        return [
            'name'       => $this->getParam('theme'),
            'responsive' => true,
            'icons'      => false,
        ];
    }

    /**
     * @return boolean
     */
    protected function isSelectAll()
    {
        return $this->getParam(static::PARAM_SELECT_ALL);
    }

    public function shouldShowSelectAll()
    {
        return $this->isSelectAll();
    }

    public function isActive(): bool
    {
        return (bool) $this->getWidgetData();
    }

    public function getWidgetData(): array
    {
        return (array) $this->getParam('list');
    }

    public function getFieldType()
    {
        return static::FIELD_TYPE_COMPLEX;
    }

    protected function getFieldTemplate()
    {
        return 'tree_select/body.twig';
    }
}
