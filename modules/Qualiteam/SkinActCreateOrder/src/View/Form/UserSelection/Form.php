<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Form\UserSelection;


class Form extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'user_selection';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'select';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            \Qualiteam\SkinActCreateOrder\View\Button\PopupUserSelector::PARAM_REDIRECT_URL => new \XLite\Model\WidgetParam\TypeString(
                'URL to redirect to',
                \XLite\Core\Request::getInstance()->{\Qualiteam\SkinActCreateOrder\View\Button\PopupUserSelector::PARAM_REDIRECT_URL}
            ),
        ];
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($this->getFormDefaultParams());
    }

    /**
     * Get form default parameters
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        return [
            'order_number' => $this->getOrder() ? $this->getOrder()->getOrderNumber() : 0,
            \Qualiteam\SkinActCreateOrder\View\Button\PopupUserSelector::PARAM_REDIRECT_URL
                => $this->getParam(\Qualiteam\SkinActCreateOrder\View\Button\PopupUserSelector::PARAM_REDIRECT_URL),
        ];
    }
}