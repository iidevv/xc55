<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core;

use Includes\Utils\FileManager;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Core\PayPalConfigManager;
use PayPal\Rest\ApiContext;
use Psr\Log\LogLevel;
use XLite\Logger;
use CDev\Paypal\Core\Api\Webhooks\EventType;
use CDev\Paypal\Core\Api\Webhooks\Webhook;
use CDev\Paypal\Core\Api\Webhooks\WebhooksList;
use CDev\Paypal\Main as PaypalMain;
use CDev\Paypal\Model\Payment\Processor\PaypalCommercePlatform;

class PaypalCommercePlatformAPI
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var ApiContext
     */
    protected $apiContext;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        $expressCheckout       = PaypalMain::getPaymentMethod(PaypalMain::PP_METHOD_EC);
        $advanced              = PaypalMain::getPaymentMethod(PaypalMain::PP_METHOD_PPA);
        $paypalForMarketPlaces = PaypalMain::getPaymentMethod(PaypalMain::PP_METHOD_PFM);

        return $this->isSelfConfigured()
            && \XLite\Core\Config::getInstance()->Security->customer_security
            && (!$expressCheckout || !$expressCheckout->isEnabled())
            && (!$paypalForMarketPlaces || !$paypalForMarketPlaces->isEnabled())
            && (!$advanced || !$advanced->isEnabled());
    }

    /**
     * @return bool
     */
    public function isSelfConfigured()
    {
        return $this->config['client_id']
            && $this->config['client_secret'];
    }

    public function getWebhooks()
    {
        return WebhooksList::get('APPLICATION', $this->getApiContext());
    }

    public function createWebhook($url, $events)
    {
        $webhook = new Webhook();
        $webhook->setUrl($url);

        foreach ($events as $event) {
            $eventType = new EventType();
            $eventType->setName($event);
            $webhook->addEventType($eventType);
        }

        return $webhook->create($this->getApiContext());
    }

    public function deleteWebhook($webhookId)
    {
        Webhook::delete($webhookId, $this->getApiContext());
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ApiContext
     */
    public function getApiContext()
    {
        if ($this->apiContext === null) {
            OAuthTokenCredential::$CACHE_PATH = LC_DIR_DATA . 'paypal.auth.cache';

            $config = $this->getConfig();

            $paypalConfig = PayPalConfigManager::getInstance();
            $paypalConfig->addConfigs([
                'cache.enabled'        => true,
                'cache.FileName'       => LC_DIR_DATA . 'paypal.auth.cache',
                'log.LogEnabled'       => true,
                'log.FileName'         => Logger::generateLogFilePath('xlite'),
                'log.LogLevel'         => LogLevel::DEBUG,
                'mode'                 => $config['mode'],
                'http.CURLOPT_TIMEOUT' => 30,
            ]);

            $this->apiContext = new ApiContext(
                new OAuthTokenCredential($config['client_id'], $config['client_secret'])
            );

            $this->apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', PaypalCommercePlatform::BN_CODE);
        }

        return $this->apiContext;
    }

    /**
     * @return bool
     */
    public static function dropPayPalTokenCash()
    {
        return FileManager::deleteFile(LC_DIR_DATA . 'paypal.auth.cache');
    }
}
