<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    use AMPDetectorTrait;

    /**
     * Prepare CSS resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareCSSResources(array $resources)
    {
        if (static::isAMP()) {
            $baseStyle = 'modules/QSL/AMP/styles/initialize.less';

            foreach ($resources as $k => $resource) {
                if ($resource['less'] && !$resource['merge'] && $resource['original'] !== $baseStyle) {
                    $resources[$k]['merge'] = $baseStyle;
                }
            }
        }

        return parent::prepareCSSResources($resources);
    }
}
