<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints;

/**
 * Abstract class trackings
 */
abstract class ATrackings extends PostEndpoint
{
    /**
     * @var array $tracking
     */
    protected array $tracking = [];

    /**
     * Add single tracking param
     * More additional fields: https://www.aftership.com/docs/tracking/f0923e79349ab-detect-courier
     *
     * @param string     $param
     * @param string|int $value
     *
     * @return void
     */
    public function addTrackingParam(string $param, string|int $value): void
    {
        $this->addTrackingParams([
            $param => $value,
        ]);
    }

    /**
     * Add multiple tracking params
     *
     * @param array $param
     *
     * @return void
     */
    public function addTrackingParams(array $param): void
    {
        $this->tracking = array_merge($this->tracking, $param);
    }

    /**
     * Get data with prepared body params
     *
     * @return array
     * @throws EndpointException
     */
    public function getData(): array
    {
        $this->setBody(
            $this->getTrackingBody()
        );

        return parent::getData();
    }

    /**
     * Prepare tracking body
     *
     * @return array
     */
    protected function getTrackingBody(): array
    {
        return [
            'tracking' => $this->tracking,
        ];
    }
}