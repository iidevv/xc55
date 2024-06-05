<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\ORM\Mapping;

use Doctrine\ORM\Mapping\NamingStrategy as NamingStrategyInterface;

class NamingStrategy implements NamingStrategyInterface
{
    private NamingStrategyInterface $default;

    private NamingStrategyInterface $underscoreNumberAware;

    public function __construct(
        NamingStrategyInterface $default,
        NamingStrategyInterface $underscoreNumberAware
    ) {
        $this->default               = $default;
        $this->underscoreNumberAware = $underscoreNumberAware;
    }

    public function classToTableName($className): string
    {
        return $this->isXLite($className)
            ? $this->default->classToTableName($className)
            : $this->underscoreNumberAware->classToTableName($className);
    }

    public function propertyToColumnName($propertyName, $className = null): string
    {
        return $this->isXLite($className)
            ? $this->default->propertyToColumnName($propertyName, $className)
            : $this->underscoreNumberAware->propertyToColumnName($propertyName, $className);
    }

    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null): string
    {
        return $this->isXLite($className)
            ? $this->default->embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className, $embeddedClassName)
            : $this->underscoreNumberAware->embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className, $embeddedClassName);
    }

    public function referenceColumnName(): string
    {
        return $this->default->referenceColumnName();
    }

    public function joinColumnName($propertyName, $className = null): string
    {
        return $this->isXLite($className)
            ? $this->default->joinColumnName($propertyName, $className)
            : $this->underscoreNumberAware->joinColumnName($propertyName, $className);
    }

    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null): string
    {
        return $this->isXLite($sourceEntity)
            ? $this->default->joinTableName($sourceEntity, $targetEntity, $propertyName)
            : $this->underscoreNumberAware->joinTableName($sourceEntity, $targetEntity, $propertyName);
    }

    public function joinKeyColumnName($entityName, $referencedColumnName = null): string
    {
        return $this->isXLite($entityName)
            ? $this->default->joinKeyColumnName($entityName, $referencedColumnName)
            : $this->underscoreNumberAware->joinKeyColumnName($entityName, $referencedColumnName);
    }

    private function isXLite(string $class): bool
    {
        return strpos($class, '\\Model\\') !== false;
    }
}
