<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * Administrator profile model widget. This widget is used in the admin interface.
 * @Extender\Mixin
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    /**
     * Save current form reference and initialize the cache.
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return \QSL\LoyaltyProgram\View\Model\Profile\AdminMain
     */
    public function __construct(array $params = [], array $sections = [])
    {
        if ($this->isRewardPointsSumVisible()) {
            $this->initRewardPointsField();
        }

        parent::__construct($params, $sections);
    }

    /**
     * Check if the "Reward points" tab is visible.
     *
     * @return bool
     */
    protected function isRewardPointsSumVisible()
    {
        return $this->getModelObject() && !$this->getModelObject()->isAdmin();
    }

    /**
     * Add the reward points field to the page.
     */
    protected function initRewardPointsField()
    {
        $this->summarySchema['reward_points'] = [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
            self::SCHEMA_LABEL    => 'Available reward points',
            self::SCHEMA_REQUIRED => false,
        ];
    }
}
