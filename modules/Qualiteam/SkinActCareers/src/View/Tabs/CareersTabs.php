<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class CareersTabs extends \XLite\View\Tabs\ATabs
{
    public static function getAllowedTargets()
    {
        return ['jobs', 'interview_questions'];
    }

    protected function defineTabs()
    {
        return [
            'jobs' => [
                'weight' => 100,
                'title' => static::t('SkinActCareers Jobs tab'),
                'template' => 'modules/Qualiteam/SkinActCareers/jobs_tab.twig',

            ],
            'interview_questions' => [
                'weight' => 200,
                'title' => static::t('SkinActCareers Interview Questions tab'),
                'template' => 'modules/Qualiteam/SkinActCareers/interview_questions_tab.twig',

            ],
        ];
    }
}