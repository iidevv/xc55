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

class GA4 extends ABackendActionExecutor implements Interfaces\IBackendActionExecutor
{
    public const MEASUREMENT_PROTOCOL_ENDPOINT = 'https://www.google-analytics.com/mp/collect';

    public static function getRequestURL(): string
    {
        $endpointBase = static::MEASUREMENT_PROTOCOL_ENDPOINT;

        $params = static::getRequestURLParams();

        return $endpointBase . ($params ? '?' . http_build_query($params) : '');
    }

    public static function getRequestURLParams(): array
    {
        /** @var GA\Resource\GA4 $resource */
        $resource = GA::getResource();

        return [
            'api_secret'     => $resource->getApiSecret(),
            'measurement_id' => $resource->getMeasurementId(),
        ];
    }

    public static function buildRequestBody($data): string
    {
        return json_encode($data);
    }

    public static function getActionSpecificData(IBackendAction $action): array
    {
        $result = [];

        $result['client_id'] = $action->getClientId();
        $result['events']    = [];

        $eventParams = $action->getActionData(AAction::RETURN_PART_DATA);

        if (GA::getResource()->isDebugMode()) {
            $eventParams['debug_mode'] = 1;
        }

        $event = [
            'name'   => $action::getEventAction(),
            'params' => $eventParams,
        ];

        $result['events'][] = $event;

        return $result;
    }
}
