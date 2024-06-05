<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Data\Converter;

use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Model\StatusesMap;
use XLite\Core\Database;
use XLite\Model\AEntity;
use XLite\Model\Order;
use XLite\Model\OrderItem;

class PushOrderConverter extends BaseConverter
{
    /**
     * @param Order $entity
     * @return AEntity[]
     */
    public function convert(AEntity $entity): array
    {
        /** @var StatusesMap $skuvaultStatuses */
        $skuvaultStatuses = $entity->getStatusesMapXcToSkuvault();

        return [
            'OrderId'        => $entity->getOrderNumber(),
            'OrderDateUtc'   => gmdate('Y-m-d\TH:i:s\Z', $entity->getDate()),
            'OrderTotal'     => $entity->getTotal(),
            'CheckoutStatus' => $skuvaultStatuses->getSkuvaultCheckoutStatus(),
            'PaymentStatus'  => $skuvaultStatuses->getSkuvaultPaymentStatus(),
            'SaleState'      => $skuvaultStatuses->getSkuvaultSaleState(),
            'Notes'          => $entity->getAdminNotes(),
            'ItemSkus' => array_filter(array_map(function (OrderItem $item) {
                return $item->getProduct()->isSkippedFromSync()
                    ? []
                    : [
                        'Sku'       => $item->getSku(),
                        'Quantity'  => $item->getAmount(),
                        'UnitPrice' => $item->getPrice(),
                    ];
            }, $entity->getItems()->toArray())),
            'ShippingInfo'   => [
                'ShippingStatus'  => $skuvaultStatuses->getSkuvaultShippingStatus(),
                'ShippingCarrier' => $entity->getShippingMethodName(),
                'FirstName'      => $entity->getProfile()->getShippingAddress()->getFirstname(),
                'LastName'       => $entity->getProfile()->getShippingAddress()->getLastname(),
                'PhoneNumber'    => $entity->getProfile()->getShippingAddress()->getPhone(),
                'Email'          => $entity->getProfile()->getLogin(),
                'Line1'          => $entity->getProfile()->getShippingAddress()->getStreet(),
                'City'           => $entity->getProfile()->getShippingAddress()->getCity(),
                'Region'         => $entity->getProfile()->getShippingAddress()->getStateName(),
                'Postal'         => $entity->getProfile()->getShippingAddress()->getZipcode(),
                'Country'        => $entity->getProfile()->getShippingAddress()->getCountryName(),
            ],
        ];
    }
}
