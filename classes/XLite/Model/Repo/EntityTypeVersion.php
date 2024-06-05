<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use Doctrine\ORM\Query;

/**
 * EntityTypeVersion repository
 */
class EntityTypeVersion extends \XLite\Model\Repo\ARepo
{
    /**
     * @var string[]
     */
    protected $entityTypeVersions;

    /**
     * Entity types which versions were bumped
     *
     * @var array
     */
    protected $bumpedEntityTypes = [];

    /**
     * @param string $entityType
     * @param string $version
     *
     * @return self
     */
    public function setEntityTypeVersion($entityType, $version)
    {
        $this->entityTypeVersions[$entityType] = $version;

        return $this;
    }

    /**
     * Get entity type version UUID for the specified entity type
     *
     * @param $entityType
     *
     * @return null|string
     */
    public function getEntityTypeVersion($entityType)
    {
        if (!isset($this->entityTypeVersions)) {
            $this->entityTypeVersions = $this->fetchAllEntityTypeVersions();
        }

        return $this->entityTypeVersions[$entityType] ?? null;
    }

    /**
     * @param string $entityType
     * @param string $version
     *
     * @return self
     */
    public function setBumpedEntityType($entityType)
    {
        $this->bumpedEntityTypes[$entityType] = $entityType;

        return $this;
    }

    /**
     * Get entity types which versions were bumped.
     *
     * @return array
     */
    public function getBumpedEntityTypes()
    {
        return array_values($this->bumpedEntityTypes);
    }

    /**
     * Prone to deadlocks
     *
     * @param $entityType
     * @param $newVersion
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function replaceEntityTypeVersion($entityType, $newVersion)
    {
        $em = \XLite\Core\Database::getEM();
        $conn = $em->getConnection();
        $tableName = $em->getClassMetadata('XLite\Model\EntityTypeVersion')->getTableName();
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s=%s',
            $tableName,
            implode(', ', ['version', 'entityType']),
            implode(', ', ['?, ?']),
            'version',
            '?'
        );
        $conn->executeUpdate($query, [$newVersion, $entityType, $newVersion]);
    }

    public function fetchAllEntityTypeVersions()
    {
        $entityTypeVersions = [];

        $evs = $this->createQueryBuilder('ev')
            ->select('ev')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        foreach ($evs as $ev) {
            $entityTypeVersions[$ev['entityType']] = $ev['version'];
        }

        return $entityTypeVersions;
    }
}
