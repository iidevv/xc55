<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Security extends \XLite\View\Tabs\Security
{
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), [
            'gdpr'
        ]);
    }

    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        $tabs['gdpr'] = [
            'weight'   => 100,
            'title'    => static::t('GDPR Activites'),
            'template' => 'modules/XC/GDPR/activities.twig',
        ];

        return $tabs;
    }
}
