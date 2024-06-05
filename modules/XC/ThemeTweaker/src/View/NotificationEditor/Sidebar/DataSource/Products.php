<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;

use XC\ThemeTweaker\Core\Notifications\Data;
use XLite\View\AView;

class Products extends AView implements DataSource
{
    private $data;

    public static function isApplicable(Data $data)
    {
        return in_array(
            $data->getDirectory(),
            static::getTemplateDirectories(),
            true
        );
    }

    public function __construct(Data $data)
    {
        $this->data = $data;
        parent::__construct([]);
    }

    public static function buildNew(Data $data)
    {
        return new static($data);
    }

    /**
     * @return array
     */
    protected static function getTemplateDirectories()
    {
        return [
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/data_source/products/body.twig';
    }

    /**
     * @return \XLite\Model\Product[]
     */
    protected function getProducts()
    {
        return !empty($this->data->getData()['products'])
            ? $this->data->getData()['products']
            : null;
    }

    /**
     * @param array $products
     *
     * @return string
     */
    protected function prepareSkus(array $products)
    {
        return implode(' ', array_map(static function (\XLite\Model\Product $product) {
            return '#' . $product->getSku();
        }, $products));
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->getProducts()
            ? $this->prepareSkus($this->getProducts())
            : '';
    }
}
