<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\View\ItemsList\Model;

/**
 * News messages items list
 */
class NewsMessage extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Sort modes
     *
     * @var array
     */
    protected $sortByModes = [
        'translations.name' => 'Name',
        'n.date' => 'Date',
    ];

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/News/news_messages/style.less';

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
            'date' => [
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Date'),
                static::COLUMN_TEMPLATE  => 'modules/XC/News/news_messages/cell.date.twig',
                static::COLUMN_SORT      => 'n.date',
                static::COLUMN_ORDERBY   => 100,
            ],
            'name' => [
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS    => ['required' => true],
                static::COLUMN_SORT      => 'translations.name',
                static::COLUMN_MAIN      => true,
                static::COLUMN_LINK      => 'news_message',
                static::COLUMN_ORDERBY   => 200,
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
        return 'XC\News\Model\NewsMessage';
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.news_message.blank');
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
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('news_message');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New news message';
    }

    // {{{ Behaviors

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
     * Mark list as switchyabvle (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
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
        return parent::getContainerClass() . ' news_messages';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XC\News\View\StickyPanel\ItemsList\NewsMessage';
    }

    /**
     * @return string
     */
    protected function getEditLink()
    {
        return 'news_message';
    }
}
