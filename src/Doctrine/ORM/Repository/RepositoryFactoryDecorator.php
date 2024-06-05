<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\ORM\Repository;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ObjectRepository;

use function preg_match;
use function str_replace;
use function strpos;

class RepositoryFactoryDecorator implements RepositoryFactory
{
    /**
     * @var ObjectRepository[]
     */
    private array $repositoryList = [];

    private RepositoryFactory $inner;

    public function __construct()
    {
        $this->inner = new DefaultRepositoryFactory();
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityName
     *
     * @return ObjectRepository
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        if ($this->isXLite($entityName)) {
            $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_hash($entityManager);

            if (isset($this->repositoryList[$repositoryHash])) {
                return $this->repositoryList[$repositoryHash];
            }

            return $this->repositoryList[$repositoryHash] = $this->createRepository($entityManager, $entityName);
        }

        return $this->inner->getRepository($entityManager, $entityName);
    }

    private function isXLite(string $entityName): bool
    {
        return strpos($entityName, '\\Model\\') !== false;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityName
     *
     * @return ObjectRepository
     */
    private function createRepository(EntityManagerInterface $entityManager, string $entityName): ObjectRepository
    {
        $metadata            = $entityManager->getClassMetadata($entityName);
        $repositoryClassName = $metadata->customRepositoryClassName
            ?: $this->getDefaultRepositoryClassName($entityName);

        return new $repositoryClassName($entityManager, $metadata);
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    private function getDefaultRepositoryClassName(string $entityName): string
    {
        $entityClass = ClassUtils::getRealClass($entityName);

        $repoClassName = str_replace('\\Model\\', '\\Model\\Repo\\', $entityClass);

        return class_exists($repoClassName)
            ? $repoClassName
            : 'XLite\\Model\\Repo\\Base\\' . (preg_match('/\wTranslation$/S', $entityClass) ? 'Translation' : 'Common');
    }
}
