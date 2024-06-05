<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\ItemsList\Model;

use XLite\View\Base\FormStickyPanel;

/**
 * Banner page view
 *
 */
class Banner extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'title' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Service name'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_PARAMS       => ['required' => true],
                static::COLUMN_LINK    => 'banner_edit',
            ],
            'location' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Location'),
                static::COLUMN_CLASS   => 'QSL\Banner\View\FormField\LayoutType',
                static::COLUMN_NO_WRAP => true,
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
        return 'QSL\Banner\Model\Banner';
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
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

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('banner_edit');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New banner';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }


    /**
     * Sticky panel widget PHP class.
     *
     * @return string|FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'QSL\Banner\View\StickyPanel\Banner';
    }
}
