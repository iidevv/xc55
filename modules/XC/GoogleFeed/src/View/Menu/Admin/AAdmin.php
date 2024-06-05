<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("QSL\ProductFeeds")
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['product_feeds'])) {
            $this->relatedTargets['product_feeds'] = [];
        }

        $this->relatedTargets['product_feeds'][] = 'google_shopping_groups';
        $this->relatedTargets['product_feeds'][] = 'google_feed';

        parent::__construct($params);
    }
}
