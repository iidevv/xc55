<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\StickyPanel;

use XLite\Core\Database;

/**
 * Sticky Panel widget for Comparison Shopping Websites page.
 */
class ProductFeeds extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Get the Save button widget.
     *
     * @return \QSL\ProductFeeds\View\Button\GenerateFeeds
     */
    protected function getSaveWidget()
    {
        return $this->getWidget(
            $this->getSaveWidgetParams(),
            $this->getSaveWidgetClass()
        );
    }

    protected function defineButtons()
    {
        $list = parent::defineButtons();
        if (!Database::getRepo('QSL\ProductFeeds\Model\ProductFeed')->count()) {
            unset($list['save']);
        }
        return $list;
    }

    /**
     * Get parameters for the Save button widget.
     *
     * @return array
     */
    protected function getSaveWidgetParams()
    {
        return [];
    }

    /**
     * Get the class name of the Save button widget.
     *
     * @return string
     */
    protected function getSaveWidgetClass()
    {
        return 'QSL\ProductFeeds\View\Button\GenerateFeeds';
    }

    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\QSL\ProductFeeds\Main';
    }
}
