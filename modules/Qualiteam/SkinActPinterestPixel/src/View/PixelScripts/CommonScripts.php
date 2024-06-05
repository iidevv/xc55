<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\View\PixelScripts;

use Qualiteam\SkinActPinterestPixel\Main;

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
    protected function definePinterestPixelScripts()
    {
        return $this->executeCachedRuntime(static function () {
            return [
                Main::getModulePath() . '/pixel/addtocart.js' => [],
                Main::getModulePath() . '/pixel/pagevisit.js' => [],
                Main::getModulePath() . '/pixel/checkout.js'  => [
                    static::TARGETS_IN => [
                        'checkoutSuccess',
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
    public function getPinterestPixelScripts()
    {
        $list = [];

        if (!\XLite::getController()->isAJAX() && Main::isPixelEnabled() && !\XLite::isAdminZone()) {
            $target = \XLite::getController()->getTarget();

            foreach ($this->definePinterestPixelScripts() as $script => $targets) {
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

                    return !(bool) $v;
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
