<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA;

use Exception;
use Psr\Log\LoggerInterface;
use XLite\Base;
use XLite\Core\HTTP\Request;
use XLite\InjectLoggerTrait;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action\Interfaces\IBackendAction;

abstract class ABackendActionExecutor extends Base
{
    use InjectLoggerTrait;

    public function execute(IBackendAction $action): bool
    {
        if (!$action->isApplicable()) {
            return false;
        }

        try {
            $data = static::buildActionQueryData($action);

            $request       = new Request(static::getRequestURL());
            $request->verb = 'POST';
            $request->body = static::buildRequestBody($data);
            $response      = $request->sendRequest();

            if (GA::getResource()->isDebugMode()) {
                $this->logger()->debug(
                    'Backend action',
                    [
                        'name'          => $action::getEventActionName(),
                        'request data'  => $data,
                        'response data' => $response,
                    ]
                );
            }
        } catch (Exception $e) {
            $this->logger()->error(
                $e->getMessage(),
                [
                    'name'          => $action::getEventActionName(),
                    'exception' => $e,
                    'data' => $data ?? $action
                ]
            );
        }

        return (bool) ($response ?? null);
    }

    protected static function buildActionQueryData(IBackendAction $action): array
    {
        return array_merge(
            static::getBaseData(),
            static::getActionSpecificData($action)
        );
    }

    public static function getBaseData(): array
    {
        return [];
    }

    abstract public static function getActionSpecificData(IBackendAction $action): array;

    abstract public static function getRequestURL(): string;

    /**
     * @param mixed $data
     *
     * @return string
     */
    abstract public static function buildRequestBody($data): string;

    protected function logger(): LoggerInterface
    {
        return $this->getLogger('CDev-GoogleAnalytics');
    }
}
