<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine\FixtureLoader;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

final class EntityProperties
{
    private EntityManagerInterface $entityManager;

    private array $entityProperties = [];

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function getEntityProperties(string $class): array
    {
        if (!isset($entityProperties[$class])) {
            $entityProperties[$class] = [
                $this->getFieldMappings($class),
                $this->getAssociationMappings($class),
                $this->getIdentifier($class),
            ];
        }

        return $entityProperties[$class];
    }

    private function getFieldMappings(string $class): array
    {
        $result = [];

        $metadata = $this->getClassMetadata($class);
        foreach ($metadata->fieldMappings as $field => $mapping) {
            $fieldInCamelCase = $this->convertToCamelCase($field);

            $result[$field] = [
                'getter'  => 'get' . $fieldInCamelCase,
                'setter'  => 'set' . $fieldInCamelCase,
                'mapping' => $mapping,
            ];
        }

        return $result;
    }

    private function getAssociationMappings(string $class): array
    {
        $result = [];

        $metadata = $this->getClassMetadata($class);
        foreach ($metadata->associationMappings as $field => $mapping) {
            $fieldInCamelCase = $this->convertToCamelCase($field);

            $toMany     = $this->isToMany($mapping);
            $manyToMany = $this->isManyToMany($mapping);

            $targetEntity = $mapping['targetEntity'];

            $result[$field] = [
                'many'         => $toMany,
                'many2many'    => $manyToMany,
                'getter'       => 'get' . $fieldInCamelCase,
                'setter'       => ($toMany ? 'add' : 'set') . $fieldInCamelCase,
                'identifiers'  => [],
                'entityName'   => $targetEntity,
                'mappedGetter' => null,
                'mappedSetter' => null,
            ];

            $targetMetadata = $this->getClassMetadata($targetEntity);
            foreach ($targetMetadata->getIdentifier() as $identifier) {
                $identifierInCamelCase                      = $this->convertToCamelCase($identifier);
                $result[$field]['identifiers'][$identifier] = [
                    'getter' => 'get' . $identifierInCamelCase,
                    'setter' => 'set' . $identifierInCamelCase,
                ];
            }

            if ($mapping['mappedBy']) {
                $mappedByInCamelCase = $this->convertToCamelCase($mapping['mappedBy']);

                $result[$field]['mappedGetter'] = 'get' . $mappedByInCamelCase;
                $result[$field]['mappedSetter'] = ($manyToMany ? 'add' : 'set') . $mappedByInCamelCase;
            }
        }

        return $result;
    }

    private function getIdentifier(string $class): array
    {
        $metadata = $this->getClassMetadata($class);

        return $metadata->getIdentifier();
    }

    private function convertToCamelCase(string $data): string
    {
        return \Includes\Utils\Converter::convertToUpperCamelCase($data);
    }

    private function isToMany(array $mapping): bool
    {
        return $mapping['type'] === ClassMetadataInfo::ONE_TO_MANY
            || $mapping['type'] === ClassMetadataInfo::MANY_TO_MANY;
    }

    private function isManyToMany(array $mapping): bool
    {
        return $mapping['type'] === ClassMetadataInfo::MANY_TO_MANY;
    }

    private function getClassMetadata(string $class): ClassMetadata
    {
        return $this->entityManager->getClassMetadata($class);
    }
}
