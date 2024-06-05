<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to Attributes pages (Product modify section)
 * @Extender\Mixin
 */
class Attributes extends \XLite\View\Tabs\Attributes
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if ($this->getProduct()->hasColorSwatchAttribute()) {
            $list['color_swatches'] = [
                'weight'   => 500,
                'title'    => static::t('Color Swatches settings'),
                'url_params' => [
                    'target'     => 'product',
                    'page'       => 'attributes',
                    'product_id' => $this->getProduct()->getProductId(),
                    'spage'      => 'color_swatches',
                ],
                'template' => 'modules/QSL/ColorSwatches/product/attributes/cs_settings.twig',
            ];
        }

        return $list;
    }
}
