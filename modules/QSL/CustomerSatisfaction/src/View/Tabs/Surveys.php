<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Surveys extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'surveys';
        $list[] = 'questions';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'surveys'            => [
                'weight' => 100,
                'title'  => static::t('Customer feedback'),
                'widget' => 'QSL\CustomerSatisfaction\View\Surveys',
            ],
            'questions' => [
                'weight' => 200,
                'title'  => static::t('Feedback questions'),
                'widget' => 'QSL\CustomerSatisfaction\View\Questions',
            ]
        ];
    }
}
