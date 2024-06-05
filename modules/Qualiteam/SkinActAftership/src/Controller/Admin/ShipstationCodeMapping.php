<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Controller\Admin;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use Qualiteam\SkinActAftership\Model\ShipstationCodeMapping as CodeMappingModel;

class ShipstationCodeMapping extends \XLite\Controller\Admin\AAdmin
{
    use AftershipTrait;

    protected string $errorMessage;

    public function getTitle()
    {
        return static::t('SkinActAftership code mapping');
    }

    protected function doActionUpdateItemsList()
    {
        $postedData = $this->preparePostData();

        if (!$this->isPostDataValid($postedData)) {
            $this->prepareTopMessageError();

            return $this->buildURL(
                static::getCodeMappingConfigName()
            );
        }

        parent::doActionUpdateItemsList();
    }

    protected function preparePostData(): array
    {
        $postedData = Request::getInstance()->getPostData();

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

    protected function isPostDataValid(array $data): bool
    {
        $issetPair = $this->getSlugPairData($data['new']);

        if (!$issetPair && isset($data['data'])) {
            $issetPair = $this->getSlugPairData($data['data']);
        }

        if ($issetPair) {
            $this->errorMessage = static::t('SkinActAftership the pair x is already exist', [
                'pair' => sprintf('%s - %s', array_key_first($issetPair), reset($issetPair)),
            ]);

            return false;
        }

        $duplicationSlug = $this->getShipstationSlug($data['new']);

        if ($duplicationSlug) {
            $this->errorMessage = static::t('SkinActAftership the shipstation slug x is already is exist', [
                'slug' => array_key_first($duplicationSlug),
            ]);

            return false;
        }

        return true;
    }

    protected function getSlugPairData($data): ?array
    {
        if (count($data) > 0) {
            foreach ($data as $item) {
                if ($this->hasPairOnDatabase($item)) {
                    $repo = $this->checkPairOnDatabase($item);

                    return [
                        $repo->getShipstationSlug() => $repo->getAftershipSlug(),
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

    protected function checkPairOnDatabase(array $item): ?CodeMappingModel
    {
        return Database::getRepo(CodeMappingModel::class)
            ->findOneBy(['aftership_slug' => $item['aftership_slug'], 'shipstation_slug' => $item['shipstation_slug']]);
    }

    protected function getShipstationSlug(array $data): ?array
    {
        if (count($data) > 0) {
            foreach ($data as $item) {
                if ($this->hasShipstationSlug($item['shipstation_slug'])) {
                    $codeMapping = $this->checkDuplicateShipstationSlug($item['shipstation_slug']);

                    return [
                        $codeMapping->getShipstationSlug() => $codeMapping->getAftershipSlug(),
                    ];
                }
            }
        }

        return null;
    }

    protected function hasShipstationSlug(string $shipstation_slug): bool
    {
        return (bool) $this->checkDuplicateShipstationSlug($shipstation_slug);
    }

    protected function checkDuplicateShipstationSlug(string $shipstation_slug): ?CodeMappingModel
    {
        return Database::getRepo(CodeMappingModel::class)
            ->findOneBy(['shipstation_slug' => $shipstation_slug]);
    }

    protected function prepareTopMessageError(): void
    {
        TopMessage::addError($this->errorMessage);
    }
}