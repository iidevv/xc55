<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\ItemsList\Model;

/**
 * Promoted special offer modules.
 */
class PromotedModules extends \XLite\View\AView
{
    protected $modules;

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/SpecialOffersBase/special_offers/special_offer_modules.twig';
    }

    /**
     * Returns an array of special offer related modules.
     *
     * @return array
     */
    protected function getPromotedModules()
    {
        if (!isset($this->modules)) {
            $this->modules = [
                'bxgy' => [
                    'author' => 'QSL',
                    'code' => 'SpecialOffersBuyXGetY',
                    'name' => 'Special Offers: Buy X Get Y',
                    'cssClass' => 'special-offer-mod--buy-x-get-y',
                ],
                'sxgy' => [
                    'author' => 'QSL',
                    'code' => 'SpecialOffersSpendXGetY',
                    'name' => 'Special Offers: Spend X Get Y',
                    'cssClass' => 'special-offer-mod--spend-x-get-y',
                ],
                'roulette' => [
                    'author' => 'QSL',
                    'code' => 'Roulette',
                    'name' => 'Coupon Roulette',
                    'cssClass' => 'special-offer-mod--roulette',
                ],
                'loyalty' => [
                    'author' => 'QSL',
                    'code' => 'LoyaltyProgram',
                    'name' => 'Loyalty Program',
                    'cssClass' => 'special-offer-mod--loyalty',
                ],
                'popups' => [
                    'author' => 'QSL',
                    'code' => 'PopupAnywhere',
                    'name' => 'Pop-up Anywhere',
                    'cssClass' => 'special-offer-mod--popups',
                ],
            ];

            $registry = \Includes\Utils\Module\Manager::getRegistry();
            foreach ($this->modules as $k => $m) {
                $module = $registry->getModule($m['author'], $m['code']);
                if ($module) {
                    $this->modules[$k]['name'] = $module->moduleName;
                }

                $this->modules[$k]['url'] = $registry->getModuleServiceURL($m['author'], $m['code']);
            }
        }

        return $this->modules;
    }

    public function getPromotedModuleInfo($moduleName, $fieldName)
    {
        $info = $this->getPromotedModules();

        return $info[$moduleName][$fieldName];
    }

    public function getModulesPromoLink()
    {
        return $this->buildUrl(
            'addons_list_marketplace',
            '',
            ['substring' => 'special offers']
        );
    }

    public function getScreenshotPath()
    {
        return \XLite::getInstance()->getShopURL(
            'assets/web/admin/modules/QSL/SpecialOffersBase/special_offers/promo'
        );
    }
}
