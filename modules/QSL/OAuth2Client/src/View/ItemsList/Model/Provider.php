<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\ItemsList\Model;

/**
 * Providers items list
 */
class Provider extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/OAuth2Client/providers/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        return [
            'name'                => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_LINK => 'oauth2_client_provider',
            ],
            'linkName'            => [
                static::COLUMN_NAME  => \XLite\Core\Translation::lbl('Link name'),
                static::COLUMN_CLASS => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_MAIN  => true,
            ],
            'display_in_header'   => [
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Display in site header'),
                static::COLUMN_CLASS     => '\XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff',
                static::COLUMN_EDIT_ONLY => true,
            ],
            'display_in_checkout' => [
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Display on the checkout page'),
                static::COLUMN_CLASS     => '\XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff',
                static::COLUMN_EDIT_ONLY => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineRepositoryName()
    {
        return 'QSL\OAuth2Client\Model\Provider';
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
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' oauth2-client-providers';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return 'QSL\OAuth2Client\View\StickyPanel\ItemsList\Provider';
    }

    /**
     * @inheritdoc
     */
    protected function getRightActions()
    {
        $list = parent::getRightActions();
        if (!$this->isStatic()) {
            $list[] = 'modules/QSL/OAuth2Client/providers/action.ready.twig';
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    // {{{ Behaviors

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
    protected function isSwitchable()
    {
        return true;
    }

    // }}}

    // {{{ Search

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\QSL\OAuth2Client\Model\Repo\Provider::P_ORDER_BY} = ['p.position', 'ASC'];

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    // }}}
}
