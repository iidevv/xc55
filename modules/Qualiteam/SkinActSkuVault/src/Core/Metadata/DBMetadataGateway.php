<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Metadata;

use Qualiteam\SkinActSkuVault\Core\Data\MetadataDTO;
use Qualiteam\SkinActSkuVault\Model\Metadata;
use XLite\Core\Database;

class DBMetadataGateway implements MetadataGateway
{
    /**
     * @param string $localId
     * @param string $entityType
     * @param array $externalData
     * @return void
     */
    public function createMetadata(string $localId, string $entityType, array $externalData): void
    {
        if (!$this->getByLocalId($entityType, $localId)) {
            $metadata = new Metadata();
            $metadata->setLocalId($localId);
            $metadata->setEntityType($entityType);
            $metadata->setExternalData($externalData);
            Database::getEM()->persist($metadata);
        }
    }

    /**
     * @param string $entityType
     * @param string $localId
     * @return array|null
     */
    public function getByLocalId(string $entityType, string $localId): ?array
    {
        $metadata = $this->getMetadataBySearchParams(['entityType' => $entityType, 'localId' => $localId]);

        return $metadata ? $this->getMetadataDTO($metadata) : null;
    }

    /**
     * @param string $entityType
     * @param array $externalData
     * @return array|null
     */
    public function getByExternalData(string $entityType, array $externalData): ?array
    {
        $metadata = Database::getRepo(Metadata::class)->getByExternalData($entityType, $externalData);

        return $metadata ? $this->getMetadataDTO($metadata) : null;
    }

    /**
     * Get metadata by search params
     *
     * @param array $searchParams
     *
     * @return Metadata|null
     */
    protected function getMetadataBySearchParams(array $searchParams): ?Metadata
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection NullPointerExceptionInspection */
        return Database::getRepo(Metadata::class)->findOneBy($searchParams);
    }

    /**
     * Get metadata DTO
     *
     * @param Metadata $metadata
     *
     * @return array
     */
    protected function getMetadataDTO(Metadata $metadata): array
    {
        return (new MetadataDTO())->convert($metadata);
    }
}
