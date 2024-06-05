<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XLite\Core\Request;

/**
 * Tabs related to Attributes pages (Product modify section)
 */
class Attributes extends \XLite\View\Tabs\ATabs
{
    /**
     * Widget parameter names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = [
            'custom' => [
                'weight'     => 100,
                'title'      => static::t('Product-Specific'),
                'url_params' => [
                    'target'     => 'product',
                    'page'       => 'attributes',
                    'product_id' => $this->getProduct()->getProductId(),
                    'spage'      => 'custom',
                ],
                'template'   => 'product/attributes/custom.twig',
            ],
            'global' => [
                'weight'   => 200,
                'title'    => static::t('Global'),
                'url_params' => [
                    'target'     => 'product',
                    'page'       => 'attributes',
                    'product_id' => $this->getProduct()->getProductId(),
                    'spage'      => 'global',
                ],
                'template' => 'product/attributes/global.twig',
            ],
            'hidden' => [
                'weight'   => 300,
                'title'    => static::t('Hidden attributes'),
                'url_params' => [
                    'target'     => 'product',
                    'page'       => 'attributes',
                    'product_id' => $this->getProduct()->getProductId(),
                    'spage'      => 'hidden',
                ],
                'template' => 'product/attributes/hidden.twig',
            ],
        ];

        if ($this->getProduct()->hasVisibleAttributes()) {
            $list['properties'] = [
                'weight'   => 400,
                'title'    => static::t('Sort settings'),
                'url_params' => [
                    'target'     => 'product',
                    'page'       => 'attributes',
                    'product_id' => $this->getProduct()->getProductId(),
                    'spage'      => 'properties'
                ],
                'template' => 'product/attributes/properties.twig',
            ];
        }

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs2.twig';
    }

    ///**
    // * Returns tab URL
    // *
    // * @param string $target Tab target
    // *
    // * @return string
    // */
    //protected function buildTabURL($target)
    //{
    //    return $this->buildURL(
    //        'product',
    //        '',
    //        [
    //            'page'       => 'attributes',
    //            'product_id' => $this->getProduct()->getProductId(),
    //            'spage'      => $target,
    //        ]
    //    );
    //}

    ///**
    // * Returns the current target
    // *
    // * @return string
    // */
    //protected function getCurrentTarget()
    //{
    //    return \XLite\Core\Request::getInstance()->spage ?: 'custom';
    //}

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product',
                null,
                false,
                'XLite\Model\Product'
            ),
        ];
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    protected function getTabs()
    {
        $tabs = parent::getTabs();

        if (Request::getInstance()->spage === null) {
            $tabs['custom']['selected'] = true;
        }

        return $tabs;
    }
}
