<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel form item-based form
 */
class ItemForm extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Widget parameter names
     */
    public const PARAM_ALWAYS_VISIBLE = 'alwaysVisible';

    /**
     * Buttons list (cache)
     *
     * @var array
     */
    protected $buttonsList;

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ALWAYS_VISIBLE => new \XLite\Model\WidgetParam\TypeBool('alwaysVisible', false)
        ];
    }

    /**
     * Check if the sticky panel is always visible.
     *
     * @return boolean
     */
    protected function alwaysVisible()
    {
        return $this->getParam(static::PARAM_ALWAYS_VISIBLE);
    }

    /**
     * Get buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        if (!isset($this->buttonsList)) {
            $this->buttonsList = $this->defineButtons() + $this->defineLastButtons();
        }

        return $this->buttonsList;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = [];
        $list['save'] = $this->getSaveWidget();

        return $list;
    }

    /**
     * Is product preview widget visible
     *
     * @return boolean
     */
    protected function isProductPreviewWidgetVisible()
    {
        return \XLite\Core\Request::getInstance()->target == 'product'
            && !empty(\XLite\Core\Request::getInstance()->product_id);
    }

    /**
     * Return product preview widget
     *
     * @return \XLite\View\Button\SimpleLink
     */
    protected function getProductPreviewWidget()
    {
        return $this->getWidget(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => 'Preview product page',
                \XLite\View\Button\AButton::PARAM_STYLE => 'model-button link action',
                \XLite\View\Button\Link::PARAM_BLANK    => true,
                \XLite\View\Button\Link::PARAM_LOCATION => $this->getPreviewProductURL(),
            ],
            '\XLite\View\Button\SimpleLink'
        );
    }

    /**
     * Return product preview URL
     *
     * @return string
     */
    protected function getPreviewProductURL()
    {
        return \XLite\Core\Converter::buildURL(
            'product',
            'preview',
            [
                'product_id' => \XLite\Core\Request::getInstance()->product_id,
                'shopKey'    => \XLite\Core\Auth::getInstance()->getShopKey(),
            ],
            \XLite::getCustomerScript()
        );
    }

    /*
     * Define buttons widgets that go the very end
     */
    protected function defineLastButtons(): array
    {
        $result = [];
        if ($this->isProductPreviewWidgetVisible()) {
            $result['preview-product'] = $this->getProductPreviewWidget();
        }

        return $result;
    }

    /**
     * Get "save" widget
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getSaveWidget()
    {
        return $this->getWidget(
            [
                'style'    => 'action submit',
                'label'    => $this->getSaveWidgetLabel(),
                'disabled' => true,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getSaveWidgetStyle(),
            ],
            'XLite\View\Button\Submit'
        );
    }

    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Save changes');
    }

    /**
     * Defines the style for the save button
     *
     * @return string
     */
    protected function getSaveWidgetStyle()
    {
        return 'regular-main-button';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return ($this->alwaysVisible() || $this->isProductPreviewWidgetVisible())
            ? parent::getClass() . ' always-visible'
            : parent::getClass();
    }
}
