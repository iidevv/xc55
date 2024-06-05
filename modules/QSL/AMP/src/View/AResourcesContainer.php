<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AResourcesContainer extends \XLite\View\AResourcesContainer
{
    /**
     * Get css style from resource
     *
     * @param array $resource
     *
     * @return string
     */
    protected function getAMPStyle($resource)
    {
        $style = $this->getInternalCssByResource($resource);

        $style = preg_replace('/skins\//', \XLite::getInstance()->getShopURL('') . 'skins/', $style);

        // Strip <style> tag
        $style = preg_replace('/<style[^>]*>(.*)<\/style>/s', '$1', $style);

        // Strip source mapping URLs present when CSS aggregation is disabled
        // AMP poses 50000 bytes limit on styles and this limit can easily be exceeded when source maps are on
        $style = preg_replace('|/\*# sourceMappingURL=.*|', '', $style);

        return $style;
    }

    /**
     * Return either aggregate CSS or separate stylesheets to be rendered in <style amp-custom> tag
     *
     * @return array
     */
    protected function getAMPCSSResources()
    {
        return $this->doCSSAggregation() ? $this->getAggregateCSSResources() : $this->getCSSResources();
    }
}
