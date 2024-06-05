<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action\Interfaces;

interface IAction
{
    /**
     * Check if action should be executed.
     * Checked on executing
     */
    public function isApplicable(): bool;

    /**
     * Action data itself
     *
     * @return array|mixed
     */
    public function getActionData(?int $returnParams = null);
}
