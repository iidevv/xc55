<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace;

use XLite\Core\GraphQL\ClientFactory;
use XLite\InjectLoggerTrait;

/**
 * Retriever
 */
class Retriever extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    /**
     * @var \XLite\Core\GraphQL\Client\AClient
     */
    private $client;

    /**
     * @var \Exception|null
     */
    private $lastError;

    /**
     * @param \XLite\Core\Marketplace\Query      $query
     * @param \XLite\Core\Marketplace\Normalizer $normalizer
     *
     * @return array|null
     */
    public function retrieve($query, Normalizer $normalizer)
    {
        return null;

        $this->lastError = null;

        try {
            $client = static::getClient();

            $response = $client->query((string) $query, $query->getVariables());

            /* @var \XLite\Core\GraphQL\Response $response */
            if ($response->hasErrors()) {
                $this->getLogger()->error('Request errors', ['errors' => $response->getErrors()]);
            }

            return $normalizer->normalize($response->getData());
        } catch (\XLite\Core\GraphQL\Exception\UnexpectedValue $e) {
            $this->getLogger()->error($e->getMessage(), ['errors' => $e->getErrors()]);
            $this->lastError = $e;
        } catch (\XLite\Core\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $this->lastError = $e;
        }

        return null;
    }

    /**
     * @return \Exception|null
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param \Exception|null $lastError
     */
    public function setLastError($lastError): void
    {
        $this->lastError = $lastError;
    }

    /**
     * @return \XLite\Core\GraphQL\Client\AClient
     */
    protected function getClient()
    {
        if ($this->client === null) {
            $this->client = ClientFactory::createWithXidCookie(
                $this->getBusUrl()
            );
        }

        return $this->client;
    }

    protected function getBusUrl()
    {
        return \XLite::getInstance()->getShopURL('service.php?/api');
    }

    protected function getAuthUrl()
    {
        return \XLite::getInstance()->getShopURL('service.php?/auth');
    }

    protected function getAuthCode()
    {
        return \Includes\Utils\ConfigParser::getOptions(['installer_details', 'auth_code']);
    }
}
