<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View;

/**
 * Widget displaying a list of items from an abandoned cart.
 * Used when rendering the abandonment e-mail contents.
 */
class AbandonedCartItems extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    public const PARAM_CART = 'cart';

    /**
     * Define widget parameters.
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CART => new \XLite\Model\WidgetParam\TypeObject(
                'Cart',
                null,
                false,
                '\XLite\Model\Cart'
            ),
        ];
    }

    /**
     * Return widget default template.
     *
     * Note: since this widget is displayed from two interfaces (ADMIN and CONSOLE)
     * we have to duplicate widget templates in both skins/admin/en and
     * skins/console/en directories.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AbandonedCartReminder/cart_items/body.twig';
    }

    /**
     * Get cart.
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->getParam(static::PARAM_CART);
    }

    /**
     * Return model of the image associated with the ordered product.
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return \XLite\Model\Image\Product\Image
     */
    protected function getProductImage(\XLite\Model\OrderItem $item)
    {
        $product = $item ? $item->getObject() : null;

        return $product ? $product->getImage() : null;
    }

    /**
     * Return URL to the page of the ordered product.
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return string
     */
    protected function getProductPageUrl(\XLite\Model\OrderItem $item)
    {
        $product = $item ? $item->getObject() : null;
        $params = (!$product) ? [] : [
            'product_id' => $product->getProductId(),
        ];

        $categoryId = $product ? $product->getCategoryId() : null;
        $params += (!$categoryId) ? [] : [
            'category_id' => $categoryId,
        ];

        $url = empty($params)
            ? null
            : \Includes\Utils\URLManager::getShopURL(
                \XLite\Core\Converter::buildURL(
                    'product',
                    '',
                    $params,
                    \XLite::CART_SELF
                )
            );

        return $url;
    }

    /**
     * Return the width to be used for icons of cart items
     *
     * @return integer
     */
    protected function getIconWidth()
    {
        return 145;
    }

    /**
     * Return the height to be used for icons of cart items
     *
     * @return integer
     */
    protected function getIconHeight()
    {
        return 158;
    }
}
