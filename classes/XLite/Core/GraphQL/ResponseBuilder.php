<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL;

class ResponseBuilder
{
    /**
     * @param string $responseBody
     *
     * @return Response
     *
     * @throws Exception\UnexpectedValue
     */
    public function build($responseBody)
    {
        $normalizedResponse = $this->normalizeResponse($responseBody);

        return new Response(
            $normalizedResponse['data'],
            $normalizedResponse['errors']
        );
    }

    /**
     * @param $responseBody
     *
     * @return array
     * @throws \XLite\Core\GraphQL\Exception\UnexpectedValue
     */
    protected function normalizeResponse($responseBody)
    {
        $decodedResponse = $this->parseResponse($responseBody);

        if (!array_key_exists('data', $decodedResponse)) {
            throw new Exception\UnexpectedValue(
                'No data in response structure',
                0,
                null,
                $decodedResponse['errors'] ?? []
            );
        }

        return [
            'data' => $decodedResponse['data'],
            'errors' => $decodedResponse['errors'] ?? [],
        ];
    }

    /**
     * @param $responseBody
     *
     * @return mixed
     * @throws \XLite\Core\GraphQL\Exception\UnexpectedValue
     */
    protected function parseResponse($responseBody)
    {
        $response = json_decode($responseBody, true);

        if (($error = json_last_error()) !== JSON_ERROR_NONE) {
            throw new Exception\UnexpectedValue(
                'Error JSON decode.',
                $error,
                null,
                $responseBody
            );
        }

        return $response[0];
    }
}
