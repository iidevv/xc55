<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Interfaces;

use CDev\GoogleAnalytics\Logic\Action\Interfaces\IBackendAction;

interface IBackendActionExecutor
{
    /**
     * Method to access a singleton
     *
     * @return IBackendActionExecutor
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getInstance();

    public function execute(IBackendAction $action): bool;
}
