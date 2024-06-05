<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

/**
 * The Profile model repository
 */
class QuickbooksCustomers extends \XLite\Model\Repo\ARepo
{    
    /**
     * Delete quickbooks customers
     * 
     * @param mixed $profileIds
     * 
     * @return void
     */
    public function deleteCustomers($profileIds)
    {
        if (!empty($profileIds)) {
            if (!is_array($profileIds)) {
                $profileIds = [$profileIds];
            }
            $this->createQueryBuilder('qc')
                ->andWhere('qc.profile_id in (:ids)')
                ->setParameter('ids', $profileIds)
                ->delete()
                ->getQuery()
                ->execute();
        }
    }
    
    /**
     * Check if profile record exists
     *
     * @param integer $profile_id Profile ID
     *
     * @return boolean
     */
    public function recordExists($profile_id)
    {
        $count = $this->createPureQueryBuilder('qc')
            ->select('COUNT(qc.profile_id)')
            ->andWhere('qc.profile_id = :profile_id')
            ->setParameter('profile_id', $profile_id)
            ->getSingleScalarResult();

        return ($count > 0);
    }
    
    /**
     * Get 'quickbooks_listid' by Profile ID
     *
     * @param integer $profile_id Profile ID
     *
     * @return string
     */
    public function getQuickbooksListid($profile_id)
    {
        $listid = $this->createPureQueryBuilder('qc')
            ->select('qc.quickbooks_listid')
            ->andWhere('qc.profile_id = :profile_id')
            ->setParameter('profile_id', $profile_id)
            ->getSingleScalarResult();

        return $listid;
    }
    
    /**
     * Check if customer synced
     *
     * @param integer $profile_id Profile ID
     *
     * @return boolean
     */
    public function checkCustomerSynced($profile_id)
    {
        $empty = '';
        $count = $this->createPureQueryBuilder('qc')
            ->select('COUNT(qc.profile_id)')
            ->andWhere('qc.profile_id = :profile_id')
            ->andWhere('qc.quickbooks_listid != :empty')
            ->andWhere('qc.quickbooks_editsequence != :empty')
            ->setParameter('profile_id', $profile_id)
            ->setParameter('empty', $empty)
            ->getSingleScalarResult();

        return ($count > 0);
    }
    
    /**
     * Check if customer exists
     *
     * @param integer $profile_id Profile ID
     *
     * @return boolean
     */
    public function checkCustomerExists($profile_id)
    {
        $count = $this->createPureQueryBuilder('qc')
            ->select('COUNT(qc.profile_id)')
            ->innerJoin(
                'XLite\Model\Profile', 'p', 'WITH',
                "p.profile_id = qc.profile_id"
            )
            ->andWhere('qc.profile_id = :profile_id')
            ->setParameter('profile_id', $profile_id)
            ->getSingleScalarResult();

        return ($count > 0);
    }
    
    /**
     * Get Profile ID by 'quickbooks_listid'
     *
     * @param string $listid Quickbooks List ID
     *
     * @return string|null
     */
    public function getProfileByListid($listid)
    {
        $profileId = $this->createPureQueryBuilder('qc')
            ->select('IDENTITY(qc.profile_id)')
            ->andWhere('qc.quickbooks_listid = :listid')
            ->setParameter('listid', $listid)
            ->getSingleScalarResult();

        return $profileId;
    }
}