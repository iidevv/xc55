<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Metadata;

/**
 * Interface MetadataGateway
 */
interface MetadataGateway
{
    /**
     * Create metadata
     *
     * @param string $localId
     * @param string $entityType
     * @param array $externalData
     *
     * @return void
     */
    public function createMetadata(string $localId, string $entityType, array $externalData): void;

    /**
     * Get metadata DTO by local id
     *
     * @param string $entityType
     * @param string $localId
     *
     * @return array|null
     */
    public function getByLocalId(string $entityType, string $localId): ?array;

    /**
     * Get metadata DTO by external data
     *
     * @param string $entityType
     * @param array $externalData
     *
     * @return array|null
     */
    public function getByExternalData(string $entityType, array $externalData): ?array;
}
