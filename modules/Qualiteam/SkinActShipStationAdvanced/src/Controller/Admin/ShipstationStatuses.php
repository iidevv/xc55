<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Controller\Admin;

use Qualiteam\SkinActShipStationAdvanced\Traits\ShipstationAdvancedTrait;
use XLite\Core\Database;
use XLite\Core\TopMessage;
use Qualiteam\SkinActShipStationAdvanced\Model\ShipstationStatuses as ShipstationStatusesModel;

class ShipstationStatuses extends \XLite\Controller\Admin\AAdmin
{
    use ShipstationAdvancedTrait;

    public function getTitle()
    {
        return static::t('SkinActShipStationAdvanced shipstation settings');
    }

    protected function doActionUpdateItemsList()
    {
        $postedData = $this->preparePostData();
        $issetPair  = $this->getStatusesPairData($postedData['new']);

        if (!$issetPair && isset($postedData['data'])) {
            $issetPair = $this->getStatusesPairData($postedData['data']);
        }

        if ($issetPair) {
            $this->prepareTopMessageError($issetPair);

            return $this->buildURL(
                static::getStatusesConfigName()
            );
        }

        parent::doActionUpdateItemsList();
    }

    protected function preparePostData(): array
    {
        $postedData = \XLite\Core\Request::getInstance()->getPostData();

        if ($this->isRemoveCoreNewItem()) {
            $postedData = $this->removeCoreNewItem($postedData);
        }

        if ($this->isFilterChangedItems()) {
            $postedData = $this->getFilteredChangedItems($postedData);
        }

        return $postedData;
    }

    protected function isRemoveCoreNewItem(): bool
    {
        return true;
    }

    protected function removeCoreNewItem(array $postedData): array
    {
        if (isset($postedData['new'][0])) {
            unset($postedData['new'][0]);
        }

        return $postedData;
    }

    protected function isFilterChangedItems(): bool
    {
        return true;
    }

    protected function getFilteredChangedItems(array $postedData): array
    {
        if (isset($postedData['data'])) {
            $postedData['data'] = array_filter($postedData['data'], static function ($item) {
                return $item['_changed'];
            });
        }

        return $postedData;
    }

    protected function getStatusesPairData($data): ?array
    {
        if (count($data) > 0) {
            foreach ($data as $item) {
                if ($this->hasPairOnDatabase($item)) {
                    $repo = $this->checkPairOnDatabase($item);

                    return [
                        $repo->getPaymentStatus()->getName() => $repo->getShippingStatus()->getName(),
                    ];
                }
            }
        }

        return null;
    }

    protected function hasPairOnDatabase(array $item): bool
    {
        return (bool) $this->checkPairOnDatabase($item);
    }

    protected function checkPairOnDatabase(array $item): ?ShipstationStatusesModel
    {
        return Database::getRepo(ShipstationStatusesModel::class)
            ->findOneBy(['shippingStatus' => $item['shippingStatus'], 'paymentStatus' => $item['paymentStatus']]);
    }

    protected function prepareTopMessageError(array $pair): void
    {
        TopMessage::addError('SkinActShipStationAdvanced the pair x is already exist', [
            'pair' => sprintf('%s - %s', array_key_first($pair), reset($pair)),
        ]);
    }
}
