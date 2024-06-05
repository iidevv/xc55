<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Button;

/**
 * User selection in popup
 */
class PopupUserSelector extends \XLite\View\Button\APopupButton
{
    const PARAM_REDIRECT_URL = 'redirect_url';

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/user_selection.js';
        foreach ($this->getWidgets() as $widget) {
            $list = array_merge($list, $this->getWidget(array(), $widget)->getJSFiles());
        }

        return $list;
    }

    /**
     * getCSSFiles
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        foreach ($this->getWidgets() as $widget) {
            $list = array_merge($list, $this->getWidget(array(), $widget)->getCSSFiles());
        }

        return $list;
    }

    /**
     * Defines the widgets from which the CSS/JS files must be taken
     *
     * @return array
     */
    protected function getWidgets()
    {
        return array(
            $this->getSelectorViewClass(),
            'Qualiteam\SkinActCreateOrder\View\ItemsList\Model\ProfileSelect',
            'Qualiteam\SkinActCreateOrder\View\StickyPanel\Profile\Admin\Profile',
            'Qualiteam\SkinActCreateOrder\View\SearchPanel\Profile\Admin\Main',
            'Qualiteam\SkinActCreateOrder\View\Form\UserSelection\Form',
            'XLite\View\FormField\Select\Country',
            'XLite\View\FormField\Select\State',
            'XLite\View\FormField\Input\Text\Phone',
            'Qualiteam\SkinActCreateOrder\View\Form\UserSelection\Search',
            'Qualiteam\SkinActCreateOrder\View\Pager\Admin\Model\Table'
        );
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target'        => $this->getSelectorTarget(),
            'widget'        => $this->getSelectorViewClass(),
            'redirect_url'  => $this->getParam(static::PARAM_REDIRECT_URL),
            'order_number'   => $this->getOrder()->getOrderNumber()
        ];
    }

    /**
     * Defines the target of the product selector
     * The main reason is to get the title for the selector from the controller
     *
     * @return string
     */
    protected function getSelectorTarget()
    {
        return 'user_selection';
    }

    /**
     * Defines the class name of the widget which will display the product list dialog
     *
     * @return string
     */
    protected function getSelectorViewClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\UserSelection';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_REDIRECT_URL => new \XLite\Model\WidgetParam\TypeString('URL to redirect to', ''),
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn regular-button popup-user-selection';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Select customer';
    }
}
