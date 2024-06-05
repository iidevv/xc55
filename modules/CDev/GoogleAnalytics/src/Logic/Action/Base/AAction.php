<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action\Base;

use XLite\Core\Config;
use XLite\Core\Translation;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action\Interfaces\IAction;

abstract class AAction implements IAction
{
    public const FORMAT_JSON         = 1;
    public const FORMAT_NO_FILTER    = 1 << 1;
    public const RETURN_PART_TYPE    = 1 << 2;
    public const RETURN_PART_DATA    = 1 << 3;
    public const RETURN_PART_CONTEXT = 1 << 4;

    /**
     * @var array|mixed
     */
    protected $requestData;

    /**
     * @var array|mixed
     */
    protected $context;

    /**
     * @param mixed $item
     * @param mixed $key
     *
     * @return bool
     */
    protected static function filterCallback($item, $key): bool
    {
        return !empty($item);
    }

    public function getActionData(?int $returnParams = null)
    {
        if (!$this->isApplicable()) {
            return null;
        }

        $result = $this->getActionRawData();

        return $this->applyActionDataParams($result, $returnParams);
    }

    public function isApplicable(): bool
    {
        return GA::getResource()->isConfigured() && $this->isValid();
    }

    protected function isValid(): bool
    {
        return (bool) static::getActionType();
    }

    abstract protected static function getActionType(): ?string;

    protected function getActionRawData(): array
    {
        Translation::setTmpTranslationCode(Config::getInstance()->General->default_language);

        if ($this->requestData === null) {
            $this->requestData = $this->buildRequestData();
        }

        if ($this->context === null) {
            $this->context = $this->buildContext();
        }

        Translation::setTmpTranslationCode(null);

        return [
            'ga-type' => static::getActionType(),
            'data'    => $this->requestData,
            'context' => $this->context,
        ];
    }

    abstract protected function buildRequestData(): array;

    protected function buildContext(): array
    {
        return [];
    }

    /**
     * @return array|string
     */
    public function applyActionDataParams(array $actionData, int $returnParams = null)
    {
        if ($returnParams === null) {
            return $actionData;
        }

        $parts = $this->compareReturnParts($actionData, $returnParams);

        if (count($parts) === 1) {
            reset($parts);
            $returnData = current($parts);
        } elseif (count($parts) > 1) {
            $returnData = $parts;
        } else {
            $returnData = $actionData;
        }

        if (count($parts) === 1) {
            $returnData = $this->filterReturn($returnData, $returnParams);
        } else {
            foreach ($returnData as $part => $data) {
                $returnData[$part] = $this->filterReturn($data, $returnParams);
            }
        }

        return $this->formatReturn($returnData, $returnParams);
    }

    protected function compareReturnParts(array $actionData, ?int $returnParams): array
    {
        $parts = [];

        if ($returnParams & static::RETURN_PART_TYPE) {
            $parts['ga-type'] = $actionData['ga-type'] ?? '';
        }

        if ($returnParams & static::RETURN_PART_DATA) {
            $parts['data'] = $actionData['data'] ?? [];
        }

        if ($returnParams & static::RETURN_PART_CONTEXT) {
            $parts['context'] = $actionData['context'] ?? [];
        }

        return $parts;
    }

    /**
     * @param mixed    $returnData
     * @param int|null $returnParams
     *
     * @return array|mixed
     */
    protected function filterReturn($returnData, ?int $returnParams)
    {
        if ($returnParams & static::FORMAT_NO_FILTER || !is_array($returnData)) {
            return $returnData;
        }

        return static::filterRecursive($returnData, ['static', 'filterCallback'], ARRAY_FILTER_USE_BOTH);
    }

    protected static function filterRecursive(array $data, callable $callback, $params = 0): array
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = static::filterRecursive($value, $callback, $params);
            }
        }

        return array_filter($data, $callback, $params);
    }

    /**
     * @param array    $returnData
     * @param int|null $returnParams
     *
     * @return array|string
     */
    protected function formatReturn(array $returnData, ?int $returnParams)
    {
        if ($returnParams & static::FORMAT_JSON) {
            if (isset($returnData['context'])) {
                $returnData['context'] = (object) $returnData['context'];
            }
            $serialized = json_encode($returnData);
            $jsonError  = json_last_error();
            if ($serialized === false || $jsonError) {
                $serialized = '{}';
            }

            return $serialized;
        }

        return $returnData;
    }
}
