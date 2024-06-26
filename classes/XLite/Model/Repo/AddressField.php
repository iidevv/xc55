<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The "address field" model repository
 */
class AddressField extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    public const CND_ENABLED  = 'enabled';
    public const CND_REQUIRED = 'required';
    public const CND_WITHOUT_CSTATE = 'withoutCState';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Get all enabled address fields
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function findAllEnabled()
    {
        return $this->defineFindAllQuery(true)->getResult();
    }

    /**
     * Return address field service name value
     *
     * @param \XLite\Model\AddressField $field
     *
     * @return string
     */
    public function getServiceName(\XLite\Model\AddressField $field)
    {
        return $field->getServiceName();
    }

    /**
     * Get billing address-specified required fields
     *
     * @return array
     */
    public function getBillingRequiredFields()
    {
        return array_filter(
            $this->findRequiredFields(),
            static fn ($item): bool => ($item !== 'email')
        );
    }

    /**
     * Get shipping address-specified required fields
     *
     * @return array
     */
    public function getShippingRequiredFields()
    {
        return array_filter(
            $this->findRequiredFields(),
            static fn ($item): bool => ($item !== 'email')
        );
    }

    /**
     * Get all enabled and required address fields
     *
     * @return array
     */
    public function findRequiredFields()
    {
        return array_map(
            [$this, 'getServiceName'],
            $this->defineFindAllQuery(true, true)->getResult()
        );
    }

    /**
     * Get all enabled and required address fields
     *
     * @return array
     */
    public function findEnabledFields()
    {
        return array_map(
            [$this, 'getServiceName'],
            $this->defineFindAllQuery(true)->getResult()
        );
    }

    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        if (isset($data['serviceName'])) {
            $result = $this->findOneByServiceName($data['serviceName']);
        } else {
            $result = parent::findOneByRecord($data, $parent);
        }

        return $result;
    }

    /**
     * Defined query builder
     *
     * @param boolean $enabled Enabled status
     * @param boolean $required Required status
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindAllQuery($enabled = null, $required = null)
    {
        $qb = $this->createQueryBuilder('a');

        if (isset($enabled)) {
            $this->prepareCndEnabled($qb, $enabled, false);
        }

        if (isset($required)) {
            $this->prepareCndRequired($qb, $required, false);
        }

        $qb->addOrderBy('a.position', 'ASC');
        $qb->addOrderBy('a.id', 'ASC');

        return $qb;
    }

    /**
     * Prepare query builder for enabled status search
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.enabled = :enabled_value')
            ->setParameter('enabled_value', $value);
    }

    /**
     * Prepare query builder for required status search
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndRequired(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.required = :required_value')
            ->setParameter('required_value', $value);
    }

    /**
     * Prepare query builder for required status search
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndWithoutCState(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.serviceName != :cstate')
                ->setParameter('cstate', 'custom_state');
        }
    }
}
