<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Page\Admin;

/**
 * Create return widget
 */
class CreateReturn extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_ORDER = 'order';

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/Returns/order/return/style.less';

        return $list;
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
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                'XLite\Model\Order'
            ),
        ];
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/Returns/order/create_return/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible();

        $result = $result
            && \XLite\Core\Request::getInstance()->page === 'create_return';

        return $result;
    }
}
