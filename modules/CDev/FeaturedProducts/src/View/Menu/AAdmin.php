<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\Menu;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (\XLite\Core\Request::getInstance()->page === 'products') {
            $this->relatedTargets['product_list'] ??= [];
            $this->relatedTargets['product_list'][] = 'featured_products';
        } else {
            $this->relatedTargets['root_categories'] ??= [];
            $this->relatedTargets['root_categories'][] = 'featured_products';
        }

        parent::__construct($params);
    }
}
