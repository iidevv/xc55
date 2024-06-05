<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Input;

use XLite\Core\Layout;

class BannerPagePosition extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    public const PARAM_POSITION = 'position';

    public const POSITION_PRODUCT_PAGE = 'product_page';
    public const POSITION_CART_PAGE    = 'cart_page';
    public const POSITION_CHECKOUT     = 'checkout';

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_POSITION => new \XLite\Model\WidgetParam\TypeString('Position', '')
        ];
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/Paypal/form_field/banner_page_position.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @return mixed|null
     */
    public function getImageUrl()
    {
        return $this->getBanners()[$this->getBannerPosition()] ?? null;
    }

    /**
     * @return array
     */
    protected function getBanners()
    {
        return [
            'product_page' => Layout::getInstance()->getResourceWebPath(
                'modules/CDev/Paypal/images/product_page.svg',
                Layout::WEB_PATH_OUTPUT_URL,
                \XLite::INTERFACE_WEB,
                \XLite::ZONE_ADMIN
            ),
            'cart_page' => Layout::getInstance()->getResourceWebPath(
                'modules/CDev/Paypal/images/cart_page.svg',
                Layout::WEB_PATH_OUTPUT_URL,
                \XLite::INTERFACE_WEB,
                \XLite::ZONE_ADMIN
            ),
            'checkout' => Layout::getInstance()->getResourceWebPath(
                'modules/CDev/Paypal/images/checkout.svg',
                Layout::WEB_PATH_OUTPUT_URL,
                \XLite::INTERFACE_WEB,
                \XLite::ZONE_ADMIN
            )
        ];
    }

    /**
     * @return mixed
     */
    public function getBannerPosition()
    {
        return $this->getParam(static::PARAM_POSITION);
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/banner_page_position.less';

        return $list;
    }
}
