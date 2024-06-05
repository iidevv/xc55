<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model\Repo;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Repository class for the Offer Type model.
 */
class OfferType extends \XLite\Model\Repo\Base\I18n
{
    use ExecuteCachedTrait;

    public const SEARCH_ENABLED = 'enabled';

    /**
     * Allowed sort criteria
     */
    public const ORDER_BY_POSITION = 'o.position';
    public const ORDER_BY_NAME     = 'o.name';

    /**
     * Returns the list of available offer types.
     *
     * @param boolean $countOnly Return items list or only its size OPTIONAL
     *
     * @return array
     */
    public function findActiveOfferTypes($countOnly = false)
    {
        return $this->search(
            $this->getActiveOfferTypesConditions(),
            $countOnly ? static::SEARCH_MODE_COUNT : static::SEARCH_MODE_ENTITIES
        );
    }

    /**
     * Returns search conditions for retrieving active offer types.
     *
     * @return \XLite\Core\CommonCell
     */
    public function getActiveOfferTypesConditions()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::P_ORDER_BY} = [static::ORDER_BY_POSITION, 'ASC'];
        $cnd->{self::SEARCH_ENABLED} = true;

        return $cnd;
    }

    /**
     * Prepare Enabled/Disabled search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 'o.enabled <> 0' : 'o.enabled = 0');
        }
    }

    /**
     * Find type id by processor class
     *
     * @param string $class Processor Class name
     *
     * @return integer
     */
    public function findTypeIdByClass(string $class)
    {
        return $this->executeCachedRuntime(function () use ($class) {
            $className = '\\' . $class;
            foreach ($this->getOfferTypes() as $type) {
                if ($className === $type->getProcessorClass()) {
                    return $type->getTypeId();
                }
            }

            return null;
        }, ['findTypeIdByClass', $class]);
    }

    /**
     * @return \QSL\SpecialOffersBase\Model\OfferType[]
     */
    public function getOfferTypes()
    {
        return $this->executeCachedRuntime(function () {
            return $this->findAll();
        }, ['getOfferTypes']);
    }
}
