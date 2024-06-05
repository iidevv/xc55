<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model;

use XCart\Extender\Mapping\Extender;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 * @ORM\HasLifecycleCallbacks
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Timestamp of last send email for buy pro membership event
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $last_pro_membership_email = 0;

    /**
     * @return int
     */
    public function getLastProMembershipEmail()
    {
        return $this->last_pro_membership_email;
    }

    /**
     * @param $last_pro_membership_email
     * @return Profile
     */
    public function setLastProMembershipEmail($last_pro_membership_email): Profile
    {
        $this->last_pro_membership_email = $last_pro_membership_email;
        return $this;
    }
}