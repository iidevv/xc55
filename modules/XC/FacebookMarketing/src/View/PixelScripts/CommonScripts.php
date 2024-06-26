<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\PixelScripts;

/**
 * CommonScripts
 */
class CommonScripts extends \XLite\Base
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    public const TARGETS_IN     = 'in';
    public const TARGETS_NOT_IN = 'not_in';
    public const CONDITIONS     = 'conditions';

    /**
     * Return list scripts and related condition
     *
     * @return mixed
     */
    protected function defineFacebookPixelScripts()
    {
        return $this->executeCachedRuntime(static function () {
            return [
                'modules/XC/FacebookMarketing/pixel/search.js'             => [
                    static::TARGETS_IN => [
                        'search'
                    ]
                ],
                'modules/XC/FacebookMarketing/pixel/view_content.js'       => [
                    static::TARGETS_IN => [
                        'main',
                        'category',
                        'page',
                        'sale_products',
                        'coming_soon',
                        'new_arrivals',
                        'bestsellers',
                    ]
                ],
                'modules/XC/FacebookMarketing/pixel/product.js'            => [],
                'modules/XC/FacebookMarketing/pixel/add_to_cart.js'        => [],
                'modules/XC/FacebookMarketing/pixel/initiate_checkout.js'  => [
                    static::TARGETS_IN => [
                        'checkout'
                    ],
                ],
                'modules/XC/FacebookMarketing/pixel/purchase.js'           => [
                    static::TARGETS_IN => [
                        'checkoutSuccess'
                    ],
                ],
            ];
        });
    }

    /**
     * Return processed scripts list
     *
     * @return array
     */
    public function getFacebookPixelScripts()
    {
        $list = [];

        if (!\XLite::getController()->isAJAX() && \XC\FacebookMarketing\Main::isPixelEnabled() && !\XLite::isAdminZone()) {
            $target = \XLite::getController()->getTarget();

            foreach ($this->defineFacebookPixelScripts() as $script => $targets) {
                if (!empty($targets[static::TARGETS_IN]) && !in_array($target, $targets[static::TARGETS_IN])) {
                    continue;
                }

                if (!empty($targets[static::TARGETS_NOT_IN]) && in_array($target, $targets[static::TARGETS_NOT_IN])) {
                    continue;
                }

                $conditionsFilter = static function ($v) {
                    if (is_callable($v)) {
                        $v = call_user_func($v);
                    }

                    return !(bool)$v;
                };

                if (!empty($targets[static::CONDITIONS]) && array_filter($targets[static::CONDITIONS], $conditionsFilter)) {
                    continue;
                }

                $list[] = $script;
            }
        }

        return $list;
    }
}
