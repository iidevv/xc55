<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Model\Repo;

/**
 * Providers repository
 */
class ExternalProfile extends \XLite\Model\Repo\ARepo
{
    public const SEARCH_PROFILE = 'profile';

    /**
     * Get active providers
     *
     * @param string                                        $id       External ID
     * @param \QSL\OAuth2Client\Model\Provider $provider Provider
     *
     * @return \QSL\OAuth2Client\Model\ExternalProfile
     */
    public function findOneByExternalIdAndProvider($id, \QSL\OAuth2Client\Model\Provider $provider)
    {
        return $this->defineQueryBuilderFindOneByExternalIdAndProvider($id, $provider)->getSingleResult();
    }

    /**
     * Check - exists for user external profile for specified provider or not
     *
     * @param \XLite\Model\Profile                          $profile  Profile
     * @param \QSL\OAuth2Client\Model\Provider $provider Provider
     *
     * @return boolean
     */
    public function isExistsProvider(\XLite\Model\Profile $profile, \QSL\OAuth2Client\Model\Provider $provider)
    {
        return (bool)$this->defineQueryBuilderIsExistsProvider($profile, $provider)->getSingleScalarResult();
    }

    /**
     * Get query builder for findOneByExternalIdAndProvider() method
     *
     * @param string                                        $id       External ID
     * @param \QSL\OAuth2Client\Model\Provider $provider Provider
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryBuilderFindOneByExternalIdAndProvider($id, \QSL\OAuth2Client\Model\Provider $provider)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.external_id = :id AND p.provider = :provider')
            ->setParameter('id', $id)
            ->setParameter('provider', $provider);
    }

    /**
     * Get query builder for isExistsProvider() method
     *
     * @param \XLite\Model\Profile                          $profile  Profile
     * @param \QSL\OAuth2Client\Model\Provider $provider Provider
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryBuilderIsExistsProvider(\XLite\Model\Profile $profile, \QSL\OAuth2Client\Model\Provider $provider)
    {
        return $this->createQueryBuilder('p')
            ->selectCount()
            ->andWhere('p.profile = :profile AND p.provider = :provider')
            ->setParameter('profile', $profile)
            ->setParameter('provider', $provider);
    }

    // {{{ Search

    /**
     * @inheritdoc
     */
    protected function getQueryBuilderForSearch()
    {
        return parent::getQueryBuilderForSearch()
            ->linkInner('e.provider');
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array|string                            $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     */
    protected function prepareCndProfile(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_object($value) && $value instanceof \XLite\Model\Profile) {
            $queryBuilder->andWhere('e.profile = :profile')
                ->setParameter('profile', $value);
        } elseif (is_int($value) || is_numeric($value)) {
            $queryBuilder->linkInner('e.profile')
                ->andWhere('profile.profile_id = :profileid')
                ->setParameter('profileid', $value);
        }
    }

    // }}}
}
