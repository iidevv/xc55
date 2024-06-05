<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class TablePrefixEventListener
{
    protected array $config;

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable([
                'name' => $this->getPrefix($classMetadata->getName(), $classMetadata->getTableName()) . $classMetadata->getTableName()
            ]);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide']) {
                $mappedTableName = $mapping['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->getPrefix($mapping['targetEntity'], $mappedTableName) . $mappedTableName;
            }
        }
    }

    /**
     * @param string $className
     * @param string $tableName
     *
     * @return string
     */
    protected function getPrefix(string $className, string $tableName): string
    {
        $prefix = $this->suggestPrefix($className, $tableName);

        if (!$prefix) {
            return '';
        }

        if (strpos($tableName, $prefix) === 0) {
            return '';
        }

        return $prefix . '_';
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    protected function suggestPrefix(string $className, string $tableName): string
    {
        $fallbackPattern = '';
        foreach ($this->config as ['class' => $classPattern, 'table' => $tablePattern, 'prefix' => $prefix]) {
            if ($classPattern === '*' && $tablePattern = '*') {
                $fallbackPattern = $prefix;

                continue;
            }

            $isClassMatch = ($classPattern === '*') || preg_match($classPattern, $className);
            $isTableMatch = ($tablePattern === '*') || preg_match($tablePattern, $tableName);

            if ($isClassMatch && $isTableMatch) {
                return $prefix;
            }
        }

        return $fallbackPattern;
    }
}
