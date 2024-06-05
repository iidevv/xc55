<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XLite\Core\View\DynamicWidgetInterface;
use XLite\Model\WidgetParam\TypeInt;

/**
 * AView
 */
class CommonAddButton extends \XLite\View\AView implements DynamicWidgetInterface
{
    /**
     * Widget parameters
     */
    public const PARAM_PRODUCT_ID     = 'productId';

    protected $product;

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT_ID => new TypeInt('ProductId'),
        ];
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if (!$this->product) {
            $this->product = \XLite\Core\Database::getRepo('\XLite\Model\Product')
                ->find($this->getParam(static::PARAM_PRODUCT_ID));
        }

        return $this->product;
    }

    /**
     * @return mixed
     */
    protected function getProductId()
    {
        return $this->getParam(static::PARAM_PRODUCT_ID);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/MyWishlist/common_add_button/body.twig';
    }

    public function isLogged()
    {
        return \XLite\Core\Auth::getInstance()->isLogged();
    }
}
