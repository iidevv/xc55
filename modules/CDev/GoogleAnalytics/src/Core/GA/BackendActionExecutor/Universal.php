<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\BackendActionExecutor;

use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Core\GA\ABackendActionExecutor;
use CDev\GoogleAnalytics\Core\GA\Interfaces;
use CDev\GoogleAnalytics\Logic\Action\Base\AAction;
use CDev\GoogleAnalytics\Logic\Action\Interfaces\IBackendAction;

class Universal extends ABackendActionExecutor implements Interfaces\IBackendActionExecutor
{
    public const MEASUREMENT_PROTOCOL_ENDPOINT = 'https://www.google-analytics.com/collect';

    public static function getRequestURL(): string
    {
        return static::MEASUREMENT_PROTOCOL_ENDPOINT;
    }

    public static function buildRequestBody($data): string
    {
        return http_build_query($data, null, '&');
    }

    public static function getBaseData(): array
    {
        $result = parent::getBaseData();

        $result['v']   = 1;
        $result['t']   = 'event';
        $result['ni']  = 1;
        $result['ec']  = 'Admin area changes';
        $result['ea']  = 'Backend action';
        $result['tid'] = GA::getResource()->getMeasurementId();

        $result['dh'] = $_SERVER['HTTP_HOST'];
        $result['dp'] = $_SERVER['REQUEST_URI'];
        $result['dt'] = 'Backend';

        return $result;
    }

    public static function getActionSpecificData(IBackendAction $action): array
    {
        $result = [];

        $result['ec'] = $action::getEventCategory();
        $result['ea'] = $action::getEventActionName();
        $result['pa'] = $action::getEventAction();

        $result['cid'] = $action->getClientId();

        return array_merge($result, $action->getActionData(AAction::RETURN_PART_DATA));
    }
}
