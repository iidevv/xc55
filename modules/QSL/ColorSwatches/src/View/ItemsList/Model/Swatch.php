<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\ItemsList\Model;

/**
 * Swatches items list
 */
class Swatch extends \XLite\View\ItemsList\Model\Table
{
    public const PARAM_SEARCH_KEYWORDS = 'keywords';

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ColorSwatches/swatches/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ColorSwatches/swatches/controller.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        return [
            'name'  => [
                static::COLUMN_NAME                              => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_CLASS                             => 'XLite\View\FormField\Inline\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_REQUIRED => true,
                static::COLUMN_ORDERBY                           => 100,
            ],
            'color' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Color'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\ColorPicker',
                static::COLUMN_ORDERBY => 200,
            ],
            'image' => [
                static::COLUMN_NAME    => static::t('Pattern'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\FileUploader\Image',
                static::COLUMN_PARAMS  => ['required' => false],
                static::COLUMN_ORDERBY => 300,
            ],
            'defaultValue' => [
                static::COLUMN_NAME      => static::t('Default'),
                static::COLUMN_TEMPLATE  => $this->getDefaultActionTemplate(),
                static::COLUMN_ORDERBY   => 400,
            ]
        ];
    }

    /**
     * @return bool
     */
    protected function isDefault()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function getLeftActions()
    {
        return array_filter(parent::getLeftActions(), function ($item) {
            return $item !== $this->getDefaultActionTemplate();
        });
    }

    /**
     * Set default swatch if there wasn't defined early
     */
    public function process()
    {
        parent::process();

        if (
            !isset($this->getRequestData()['defaultValue'])
            && $defaultEntity = array_shift($this->getPageData())
        ) {
            $defaultEntity->setDefaultValue(true);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * @inheritdoc
     */
    protected function defineRepositoryName()
    {
        return 'QSL\ColorSwatches\Model\Swatch';
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy()
    {
        return ['s.position', 'asc'];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' swatches';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return 'QSL\ColorSwatches\View\StickyPanel\ItemsList\Swatch';
    }

    // {{{ Behaviors

    /**
     * @inheritdoc
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * @inheritdoc
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    // }}}

    // {{{ Search

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        return [
            \QSL\ColorSwatches\Model\Repo\Swatch::SEARCH_KEYWORDS   => static::PARAM_SEARCH_KEYWORDS,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\QSL\ColorSwatches\Model\Repo\Swatch::SEARCH_ORDERBY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'QSL\ColorSwatches\View\SearchPanel\Swatch\Main';
    }

    /**
     * Default value for PARAM_WRAP_WITH_FORM
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'color_swatches';
    }

    public function isSearchVisible(): bool
    {
        return true;
    }
    // }}}
}
