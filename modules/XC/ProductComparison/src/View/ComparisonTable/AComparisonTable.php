<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductComparison\View\ComparisonTable;

/**
 * Comparison table (absrtact)
 *
 */
abstract class AComparisonTable extends \XLite\View\AView
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison/comparison_table';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}
