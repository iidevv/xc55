<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\AddToCompare;

use XCart\Extender\Mapping\Extender;
use XLite\Core\PreloadedLabels\ProviderInterface;
use XC\ProductComparison\Core\Data;

/**
 * Product comparison widget
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductComparison")
 */
class ProductCompareIndicator extends \XC\ProductComparison\View\AddToCompare\ProductCompareIndicator implements ProviderInterface
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductComparison/header_widget.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/XC/ProductComparison/header_widget.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductComparison/header_indicator.twig';
    }

    /**
     * Return compared count
     *
     * @return int
     */
    protected function getComparedCount()
    {
        return Data::getInstance()->getProductsCount();
    }

    /**
     * Check if recently updated
     *
     * @return boolean
     */
    protected function isRecentlyUpdated()
    {
        return Data::getInstance()->isRecentlyUpdated();
    }

    /**
     * Return compare url
     *
     * @return string
     */
    protected function getCompareURL()
    {
        return $this->buildURL('compare');
    }

    /**
     * Check if disabled
     *
     * @return bool
     */
    protected function isDisabled()
    {
        return $this->getComparedCount() < 2;
    }

    /**
     * Return title message
     *
     * @return string
     */
    protected function getLinkHelpMessage()
    {
        return $this->isDisabled()
            ? static::t('Please add another product to comparison')
            : static::t('Go to comparison table');
    }

    /**
     * Get preloaded labels
     *
     * @return array
     */
    public function getPreloadedLanguageLabels()
    {
        $list = [
            'Please add another product to comparison',
            'Go to comparison table',
        ];

        $data = [];
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }

    /**
     * Return list of indicator element classes
     *
     * @return array
     */
    protected function getIndicatorClassesList()
    {
        $list = [];

        if ($this->isDisabled()) {
            $list[] = 'disabled';
        }

        if ($this->getComparedCount() > 0 && $this->isRecentlyUpdated()) {
            $list[] = 'recently-updated';
        }

        return $list;
    }

    /**
     * Return indicator element classes
     *
     * @return string
     */
    protected function getIndicatorClasses()
    {
        return implode(' ', $this->getIndicatorClassesList());
    }
}
