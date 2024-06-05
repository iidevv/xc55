<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\NotificationEditor\Sidebar\DataSource;

use XC\ThemeTweaker\Core\Notifications\Data;
use XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\DataSource;
use XLite\View\AView;

class ProductVariant extends AView implements DataSource
{
    private $data;

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductVariants/notification_editor/sidebar/data_source/product_variant/body.twig';
    }

    public static function isApplicable(Data $data)
    {
        return in_array(
            $data->getDirectory(),
            static::getTemplateDirectories(),
            true
        );
    }

    protected static function getTemplateDirectories()
    {
        return [
            'modules/XC/ProductVariants/low_variant_limit_warning',
        ];
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
     * @return \XC\ProductVariants\Model\ProductVariant|null
     */
    protected function getProductVariant()
    {
        return $this->data->getData()['product_variant'] ?? null;
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getVariantId()
            : '';
    }
}
