<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View;

/**
 * Enables caching for a widget
 */
trait CarouselDataAttributesTrait
{
    /**
     * @return mixed
     */
    protected function getProductsCarouselConfig()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductsCarousel;
    }

    /**
     * @return mixed
     */
    protected function isCarousel()
    {
        $block_code = $this->getBlockCode();

        return $this->getProductsCarouselConfig()->{$block_code};
    }

    /**
     * Get widget tag attributes
     *
     * @return array
     */
    protected function getWidgetTagAttributes()
    {
        $data = parent::getWidgetTagAttributes();

        if ($this->isCarousel()) {
            $data['data-carousel'] = 1;
        }

        return $data;
    }

}
