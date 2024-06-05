<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * ImageSettings repository
 */
class ImageSettings extends \XLite\Model\Repo\ARepo
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['code', 'model', 'moduleName'],
    ];

    /**
     * @param array                     $record
     * @param \XLite\Model\AEntity|null $parent
     * @param array                     $parentAssoc
     *
     * @return \XLite\Model\AEntity
     */
    public function loadFixture(
        array $record,
        \XLite\Model\AEntity $parent = null,
        array $parentAssoc = []
    ) {
        $moduleName = $this->yamlLoadingOptions->getOption('moduleName')
            ?: \XLite\Core\Skin::SKIN_STANDARD;

        if (!isset($record['moduleName'])) {
            $record['moduleName'] = $moduleName;
        }

        return parent::loadFixture($record, $parent, $parentAssoc);
    }

    /**
     * Find by module name
     *
     * @param  string $moduleName Module name
     *
     * @return array
     */
    public function findByModuleName($moduleName)
    {
        $queryBuilder = $this->createQueryBuilder()
            ->andWhere('i.moduleName = :moduleName')
            ->setParameter('moduleName', $moduleName);

        if ($moduleName === \XLite\Core\Skin::SKIN_STANDARD) {
            $queryBuilder
                ->orWhere('i.moduleName = :empty')
                ->setParameter('empty', '');
        }

        return $queryBuilder->getResult();
    }
}
