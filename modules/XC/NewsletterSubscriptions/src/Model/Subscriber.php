<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscriber
 *
 * @ORM\Entity
 * @ORM\Table  (name="newsletter_subscriptions_subscribers",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint (name="email", columns={"email"})
 *     }
 * )
 */
class Subscriber extends \XLite\Model\AEntity
{
    /**
     * Product unique ID
     *
     * @var   integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Code
     *
     * @var   string
     *
     * @ORM\Column (type="string")
     */
    protected $email;

    /**
     * Enabled status
     *
     * @var   boolean
     *
     * @ORM\ManyToOne(targetEntity="XLite\Model\Profile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="profile_id", nullable=true, onDelete="SET NULL")
     */
    protected $profile;

    /**
     * @param string $email
     *
     * @return \XC\NewsletterSubscriptions\Model\Subscriber
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return \XC\NewsletterSubscriptions\Model\Subscriber
     */
    public function setProfile(\XLite\Model\Profile $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
