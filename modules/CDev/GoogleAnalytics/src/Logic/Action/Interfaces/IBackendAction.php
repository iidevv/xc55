<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action\Interfaces;

interface IBackendAction extends IAction
{
    public static function getEventCategory(): string;

    public static function getEventActionName(): string;

    public static function getEventAction(): string;

    public function getClientId(): string;
}
