<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Utils\Module\Manager;
use XLite\Core\Cache\ExecuteCachedTrait;

class Promo extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    /**
     * @param $id
     *
     * @return string
     */
    public function getPromoContent($id)
    {
        $result    = '';
        $promoData = $this->getPromoData($id);

        if (!$promoData) {
            return $result;
        }

        if (isset($promoData['module'])) {
            $url = $this->getRecommendedModuleURL($promoData['module']);

            if ($url) {
                $result = static::t($promoData['content'], ['url' => $url]);
            }
        } else {
            $result = $promoData['content'];
        }

        return $result;
    }

    /**
     * @param $id
     *
     * @return mixed|null
     */
    public function getPromoData($id)
    {
        $list = $this->getPromoList();

        return $list[$id] ?? null;
    }

    /**
     * Get recommended module URL
     *
     * @param string $moduleName
     *
     * @return string
     */
    public function getRecommendedModuleURL($moduleName)
    {
        return !Manager::getRegistry()->isModuleEnabled($moduleName)
            ? Manager::getRegistry()->getModuleServiceURL($moduleName)
            : '';
    }

    /**
     * @return array
     */
    protected function getPromoList()
    {
        return [
            'multi-currency-1'      => [
                'module'  => 'XC-MultiCurrency',
                'content' => 'Need a way to set multicurrency prices? [Install the addon]',
            ],
            'wholesale-prices-1'    => [
                'module'  => 'CDev-Wholesale',
                'content' => 'Need a way to set wholesale prices? [Install the addon]',
            ],
            'banner-system-1'       => [
                'module'  => 'QSL-Banner',
                'content' => 'Get a more powerful banner system for your store',
            ],
            'pdf-invoice-1'         => [
                'module'  => 'QSL-PDFInvoice',
                'content' => 'Get a more customizeable PDF invoice solution for your store',
            ],
            'seo-promo-1'           => [
                'content' => static::t('Want help with SEO? Ask X-Cart Guru', [
                    'url' => \XLite::getXCartURL('https://www.x-cart.com/seo-consulting.html'),
                ]),
            ],
            'advanced-contact-us-1' => [
                'module'  => 'QSL-AdvancedContactUs',
                'content' => 'Need a customizable contact us form with location map? [Get it now!]',
            ],
            'shopper-approved-1'    => [
                'module'  => 'XC-ShopperApproved',
                'content' => 'Or add a video review powered by ShopperApproved',
            ],
            'shopper-approved-2'    => [
                'module'  => 'XC-ShopperApproved',
                'content' => 'Want to customize review surveys and display video testimonials? Try Shopper Approved and collect up to 70x more ratings and reviews',
            ],
            'live-chat-1' => [
                'content' => static::t('Don’t lose any more sales that are coming to your website. LiveChat gives you an opportunity to engage in real-time conversations and convert more visitors before they leave. Create a LiveChat account here.'),
            ],
        ];
    }
}
