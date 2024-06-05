<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\MarketPrice\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Price extends \XLite\View\Price
{
    public const MARKET_PRICE_LABEL = 'market_price_label';

    protected $marketPriceLabel = null;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/MarketPrice/style.css';

        return $list;
    }

    /**
     * Determine if we need to display product market price
     *
     * @return boolean
     */
    protected function isShowMarketPrice()
    {
        return 0 < $this->getListPrice()
            && $this->getFormattedMarketPrice() > $this->getListPrice();
    }

    /**
     * Get the "You save" value
     *
     * @return float
     */
    public function getSaveDifference()
    {
        return $this->getFormattedMarketPrice() - $this->getListPrice();
    }

    /**
     * Return the "x% label" element
     *
     * @return array
     */
    protected function getLabels()
    {
        return parent::getLabels() + [$this->getMarketPriceLabel()];
    }

    /**
     * Return the specific market price label info
     *
     * @return array
     */
    public function getMarketPriceLabel()
    {
        if (is_null($this->marketPriceLabel) && $this->isShowMarketPrice()) {
            $percent = 0;

            if ($this->getProduct()->getMarketPrice()) {
                $percent = min(99, round(($this->getSaveDifference() / $this->getFormattedMarketPrice()) * 100));
            }

            if (0 < $percent) {
                $this->marketPriceLabel['green market-price'] =
                    sprintf(\XLite\Core\Config::getInstance()->Units->percent_format, $percent)
                    . ' ' . static::t('less');
            }

            \CDev\MarketPrice\Core\Labels::addLabel($this->getProduct(), $this->marketPriceLabel);
        }

        return $this->marketPriceLabel;
    }

    /**
     * Return the specific label info
     *
     * @param string $labelName
     *
     * @return array
     */
    protected function getLabel($labelName)
    {
        return $labelName === static::MARKET_PRICE_LABEL
            ? $this->getMarketPriceLabel()
            : parent::getLabel($labelName);
    }

    /**
     * @return float
     */
    protected function getFormattedMarketPrice()
    {
        $marketPrice = $this->getProduct()->getDisplayMarketPrice();
        return \XLite::getInstance()->getCurrency()->roundValue($marketPrice);
    }
}
