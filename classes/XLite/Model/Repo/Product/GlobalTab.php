<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Product;

class GlobalTab extends \XLite\Model\Repo\ARepo
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['service_name'],
    ];

    /**
     * Load raw fixture
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param array                $record  Record
     * @param array                $regular Regular fields info OPTIONAL
     * @param array                $assocs  Associations info OPTIONAL
     *
     * @return void
     */
    public function loadRawFixture(
        \XLite\Model\AEntity $entity,
        array $record,
        array $regular = [],
        array $assocs = []
    ) {
        $providerCode = $this->yamlLoadingOptions->getOption('moduleName')
            ?: \XLite\Model\Product\GlobalTabProvider::PROVIDER_CORE;

        /** @var \XLite\Model\Product\GlobalTab $entity */
        if (!$entity->getProviderByCode($providerCode)) {
            /** @var \XLite\Model\Product\GlobalTabProvider $provider */
            $provider = new \XLite\Model\Product\GlobalTabProvider();
            $provider->setTab($entity);
            $provider->setCode($providerCode);
            $entity->addProvider($provider);
        }

        parent::loadRawFixture($entity, $record, $regular, $assocs);
    }
}
