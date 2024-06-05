<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Helpers;

use Qualiteam\SkinActShipStationAdvanced\Api\ShipStationApi;
use Qualiteam\SkinActShipStationAdvanced\Main;
use XC\ProductVariants\Model\ProductVariant;
use XLite\Core\Converter;
use GuzzleHttp\Exception\GuzzleException;
use XLite\InjectLoggerTrait;
use XLite\Model\Product;
use \XLite\Logger;
use Psr\Log\LoggerInterface;

class ShipstationApiHelper
{
    use InjectLoggerTrait;

    /**
     * @var ShipStationApi
     */
    protected static ShipStationApi  $shipstation;
    protected static LoggerInterface $logger;

    public function __construct(ShipStationApi $shipstationApi)
    {
        static::$shipstation = $shipstationApi;
        static::$logger      = Logger::getLogger('SkinActShipStation');
    }

    /**
     * @throws GuzzleException
     * @var $item Product|ProductVariant
     */
    public static function defineRunStep($item): void
    {
        $product = static::getShipstationProduct($item);
        $result = [];

        if (
            $product
            && $product['productId']
        ) {
            $product = static::prepareShipStationProductParams($product, $item);

            $response = static::putShipstationProduct($product);
            $result   = static::getPutProductResult($response);
        }

        if (
            Main::isDeveloperMode()
            || $result['success']
        ) {
            static::setItemSyncIsCompleted($item);
        }
    }

    /**
     * @throws GuzzleException
     */
    protected static function getShipstationProduct($item): mixed
    {
        $product = null;

        if (Main::isDeveloperMode()) {
            static::$logger->info('Developer mode is on');
            if (in_array($item->getSku(), Main::getDeveloperModeProductSkus())) {
                static::$logger->info('Isset dev product');
                static::$logger->info('ShipStation get product request');
                $shipstationRequestResult = static::getShipstationRequestResult($item);
                static::$logger->info('ShipStation result', [$shipstationRequestResult]);
            }
        } else {
            static::$logger->info('ShipStation get product');
            $shipstationRequestResult = static::getShipstationRequestResult($item);
            static::$logger->info('ShipStation result', [$shipstationRequestResult]);
        }

        if (
            isset($shipstationRequestResult['products'])
            && $shipstationRequestResult['products'][0]
        ) {
            $product = $shipstationRequestResult['products'][0];
        }

        return $product;
    }

    /**
     * @throws GuzzleException
     */
    protected static function getShipstationRequestResult($item)
    {
        return static::$shipstation->getProducts([
            'sku' => $item->getSku(),
        ]);
    }

    protected static function prepareShipStationProductParams($product, $item): array
    {
        static::$logger->info('ShipStation product before prepare params', $product);
        $product['length'] = $item->getBoxLength();
        $product['width']  = $item->getBoxWidth();
        $product['height'] = $item->getBoxHeight();
        $product['weight'] = static::prepareWeightParam($item);
        static::$logger->info('ShipStation product after prepare params', $product);

        return $product;
    }

    protected static function prepareWeightParam($item): float
    {
        $weight = $item->getWeight();

        if (
            static::isItemProductVariant($item)
            && $item->getDefaultWeight()
        ) {
            $weight = $item->getParent()->getWeight();
        }

        return Converter::convertLbsToOz($weight);
    }

    protected static function isItemProductVariant($item): bool
    {
        return $item instanceof ProductVariant;
    }

    protected static function putShipstationProduct($product)
    {
        static::$logger->info('Put shipstation product', $product);

        return static::$shipstation->putProduct($product['productId'], $product);
    }

    protected static function getPutProductResult($response): mixed
    {
        static::$logger->info('Put shipstation product result', $response);

        return $response;
    }

    protected static function setItemSyncIsCompleted($item): void
    {
        static::$logger->info('Change item param prepareToSyncShipStation');
        $item->setPrepareToSyncShipStation(false);
        $item->update();
        static::$logger->info('End');
    }
}
