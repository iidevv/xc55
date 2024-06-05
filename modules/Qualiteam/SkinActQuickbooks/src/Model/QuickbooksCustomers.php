<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * Quickbooks customers model
 *
 * @ORM\Entity
 * @ORM\Table  (name="quickbooks_customers",
 *      indexes={
 *          @ORM\Index (name="quickbooks_editsequence", columns={"quickbooks_editsequence"}),
 *          @ORM\Index (name="quickbooks_listid", columns={"quickbooks_listid"})
 *      }
 * )
 */
class QuickbooksCustomers extends AEntity
{
    /**
     * Profile ID
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\Id
     * @ORM\OneToOne   (targetEntity="XLite\Model\Profile", cascade={"merge","detach","persist"})
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $profile_id;
    
    /**
     * Quickbooks editsequence
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $quickbooks_editsequence;

    /**
     * Quickbooks listid
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $quickbooks_listid;
    
    /**
     * Get Profile Id 
     * 
     * @return integer
     */
    public function getProfileId()
    {
        return $this->profile_id;
    }

    /**
     * Set Profile Id
     * 
     * @param integer $id
     * 
     * @return QuickbooksCustomers
     */
    public function setProfileId($id)
    {
        $this->profile_id = $id;
        
        return $this;
    }
    
    /**
     * Set quickbooks editsequence
     *
     * @param string $value
     *
     * @return QuickbooksCustomers
     */
    public function setQuickbooksEditsequence($value)
    {
        $this->quickbooks_editsequence = $value;
    }
    
    /**
     * Set quickbooks listid
     *
     * @param string $value
     *
     * @return QuickbooksCustomers
     */
    public function setQuickbooksListid($value)
    {
        $this->quickbooks_listid = $value;
    }

    /**
     * Get Quickbooks Editsequence
     *
     * @return string
     */
    public function getQuickbooksEditsequence()
    {
        return $this->quickbooks_editsequence;
    }

    /**
     * Get Quickbooks Listid
     *
     * @return string
     */
    public function getQuickbooksListid()
    {
        return $this->quickbooks_listid;
    }
}