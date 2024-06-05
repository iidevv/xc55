<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->addRelatedTarget('job', 'jobs');
        $this->addRelatedTarget('interview_questions', 'jobs');
        $this->addRelatedTarget('career_question', 'jobs');
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['communications'])) {
            $list['communications'][static::ITEM_CHILDREN]['careers'] = [
                static::ITEM_TITLE => static::t('SkinActCareers Careers'),
                static::ITEM_TARGET => 'jobs',
                static::ITEM_WEIGHT => 1000,
            ];
        }

        return $list;
    }
}
