<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\View\FormField;

use CDev\Wholesale\Model\Repo\Base\AWholesalePrice;
use CDev\Wholesale\Model\Repo\WholesalePrice as WholesalePriceRepo;
use CDev\Wholesale\Model\WholesalePrice;
use CDev\Wholesale\Module\XC\ProductVariants\Model\Repo\ProductVariantWholesalePrice as ProductVariantWholesalePriceRepo;
use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;

/**
 * @Extender\Depend("XC\ProductVariants")
 */
class WholesalePrices extends \XLite\View\FormField\Inline\Label
{
    /**
     * Wholesale prices
     *
     * @var array
     */
    protected $wholesalePrices;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Wholesale/form_field/wholesale_prices.less';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Wholesale/form_field/wholesale_prices.twig';
    }

    /**
     * Return wholesale prices
     *
     * @return array
     */
    protected function getWholesalePrices()
    {
        if (!isset($this->wholesalePrices)) {
            $cnd = new CommonCell();
            $cnd->{AWholesalePrice::P_ORDER_BY_MEMBERSHIP} = true;
            $cnd->{AWholesalePrice::P_ORDER_BY} = ['w.quantityRangeBegin', 'ASC'];

            if ($this->getEntity()->getDefaultPrice()) {
                $cnd->{WholesalePriceRepo::P_PRODUCT} = $this->getEntity()->getProduct();

                $this->wholesalePrices = Database::getRepo(WholesalePrice::class)->search($cnd);
            } else {
                $cnd->{ProductVariantWholesalePriceRepo::P_PRODUCT_VARIANT} = $this->getEntity();

                $this->wholesalePrices = Database::getRepo(ProductVariantWholesalePrice::class)->search($cnd);
            }
        }

        return $this->wholesalePrices;
    }

    /**
     * Return link
     *
     * @return string
     */
    protected function getLink()
    {
        return $this->getEntity()->getDefaultPrice()
            ? $this->buildURL('product', null, ['product_id' => $this->getEntity()->getProduct()->getId(), 'page' => 'wholesale_pricing'])
            : $this->buildURL('product_variant', null, ['id' => $this->getEntity()->getId(), 'page' => 'wholesale_pricing']);
    }


    /**
     * @param \CDev\Wholesale\Model\Base\AWholesalePrice $wp
     *
     * @return string
     */
    protected function formatValue($wp)
    {
        if ($wp->getType() === $wp::WHOLESALE_TYPE_PERCENT) {
            return $wp->getPrice() . '%';
        }

        return $this->formatPrice($wp->getPrice());
    }

    /**
     * @return bool
     */
    protected function isWholesaleNotAllowed()
    {
        return $this->getEntity()->getDefaultPrice();
    }

    /**
     * @return string
     */
    protected function getWholesaleNotAllowedMessage()
    {
        $message = static::t('Set the price for this variant to define variant\'s personal wholesale prices');

        if ($this->getEntity()->getDefaultPrice() && $this->getWholesalePrices()) {
            $message .= '<br/><a href="' . $this->getLink() . '">' . static::t('View parent product\'s wholesale prices') . '</a>';
        }

        return $message;
    }
}
