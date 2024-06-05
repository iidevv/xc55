<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\StickyPanel\ItemsList;

/**
 * Records items list's sticky panel
 */
abstract class ARecord extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * @inheritdoc
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['check_stock'] = $this->getCheckStockButtonWidget();
        $list['send_all'] = $this->getSendAllButtonWidget();

        return $list;
    }

    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\QSL\BackInStock\Main';
    }

    protected function getSaveWidgetStyle(): string
    {
        return parent::getSaveWidgetStyle() . ' hide-if-empty-list';
    }

    /**
     * Get "check stock" button
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getCheckStockButtonWidget()
    {
        return $this->getWidget(
            [
                'style'       => 'action submit always-enabled check-stock hide-if-empty-list',
                'label'       => static::t('Check products'),
                'button-type' => 'regular-main-button',
                'action'      => 'check_stock',
            ],
            'XLite\View\Button\Regular'
        );
    }

    /**
     * Get "send all" button
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getSendAllButtonWidget()
    {
        return $this->getWidget(
            [
                'style'       => 'action submit always-enabled send-all hide-if-empty-list',
                'label'       => static::t('Send notifications'),
                'button-type' => 'regular-main-button',
                'action'      => 'send_all',
            ],
            'XLite\View\Button\Regular'
        );
    }
}
