<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\Core\Api;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use XLite\InjectLoggerTrait;

class GoogleMap
{
    use InjectLoggerTrait;

    protected string $key;
    protected Client $client;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->client = new Client(['base_uri' => '']);
    }

    public function getCoordinateByAddress(string $address): array
    {
        try {
            $response = $this->client->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'address'   => $address,
                    'key'       => $this->key,
                ],
            ]);

            $this->checkResponse($response);
            [
                $latitude,
                $longitude,
            ] = $this->composeResultFromRequest($response);
        } catch (\Throwable $exception) {
            [
                $latitude,
                $longitude,
            ] = $this->composeResultFromException($exception, [$address]);
        }

        return [
            $latitude,
            $longitude,
        ];
    }

    /**
     * @throws \JsonException
     */
    protected function composeResultFromRequest(ResponseInterface $response): array
    {
        $result = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $latitude  = $result['results'][0]['geometry']['location']['lat'] ?? 0;
        $longitude = $result['results'][0]['geometry']['location']['lng'] ?? 0;

        return [
            $latitude,
            $longitude,
        ];
    }

    protected function composeResultFromException(\Throwable $exception, array $context)
    {
        $latitude  = 0;
        $longitude = 0;
        $this->getLogger('GoogleMap')->error('getCoordinateByAddress', $context + [$exception]);

        return [
            $latitude,
            $longitude,
        ];
    }

    protected function checkResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(sprintf(' %s  %s', $response->getStatusCode(), $response->getBody()));
        }
    }
}
