<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\ItemsList\Model;

/**
 * Special offers items list
 */
class SpecialOffer extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'main/style.css';
        $list[] = 'modules/QSL/SpecialOffersBase/special_offers/style.css';

        return $list;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return \QSL\SpecialOffersBase\Model\Repo\SpecialOffer::ORDER_BY_POSITION;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Special offer'),
                static::COLUMN_LINK     => 'special_offer',
                static::COLUMN_ORDERBY  => 100,
            ],
            'activeFrom' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Active from'),
                static::COLUMN_TEMPLATE => 'modules/QSL/SpecialOffersBase/special_offers/parts/cell.date.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
            'activeTill' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Active till'),
                static::COLUMN_TEMPLATE => 'modules/QSL/SpecialOffersBase/special_offers/parts/cell.date.twig',
                static::COLUMN_ORDERBY  => 300,
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
        return 'QSL\SpecialOffersBase\Model\SpecialOffer';
    }


    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('special_offer');
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy()
    {
        return [$this->getSortBy(), $this->getSortOrder()];
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New special offer';
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
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

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' special_offers';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'QSL\SpecialOffersBase\View\StickyPanel\ItemsList\SpecialOffer';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $searchCase->{\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::SEARCH_TYPE_ENABLED} = true;

        return $searchCase;
    }

    /**
     * Returns an array of special offer related modules.
     *
     * @return array
     */
    protected function getPromotedModules()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Module');

        $result = [];

        $module = $repo->findOneBy(['author' => 'QSL', 'name' => 'BuyXGetY'], ['fromMarketplace' => 'ASC']);
        $result[] = [
            'cssClass' => 'special-offer-mod--buy-x-get-y',
            'name' => $module->getName(),
            'url' => $module->getMarketplaceURL(),
            'promo' => $this->t('This module adds variations of the "Buy X Get Y" special offer type. For example, you can give the 50% discount on each third product from Toys category.'),
        ];

        $module = $repo->findOneBy(['author' => 'QSL', 'name' => 'SpendXGetY'], ['fromMarketplace' => 'ASC']);
        $result[] = [
            'cssClass' => 'special-offer-mod--spend-x-get-y',
            'name' => $module->getName(),
            'url' => $module->getMarketplaceURL(),
            'promo' => $this->t('This module adds variations of the "Spend X Get Y" special offer type. For example, in every $100 spent by customer you can give the cheapest product away as a gift.'),
        ];

        return $result;
    }
}
