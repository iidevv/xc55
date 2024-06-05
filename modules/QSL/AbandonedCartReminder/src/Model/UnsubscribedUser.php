<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * User that unsubscribed from cart reminder e-mails.
 *
 * @ORM\Entity (repositoryClass="\QSL\AbandonedCartReminder\Model\Repo\UnsubscribedUser")
 * @ORM\Table  (name="cart_reminder_unsubscribed",
 *      indexes={
 *          @ORM\Index (name="unsubscribeDate", columns={"unsubscribeDate"})
 *      },
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="email", columns={"email"})
 *      }
 * )
 */
class UnsubscribedUser extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Unsubscribed e-mail.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable=false, options={ "default": "" })
     */
    protected $email = '';

    /**
     * Unix timestamp of the date when the user unsubscribed.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $unsubscribeDate = 0;

    /**
     * Returns the record identifier.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Updates the unsubscribed email.
     *
     * @param string $email Email
     *
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the unsubscribed email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Updates the date when the user unsubscribed.
     *
     * @param int $timestamp Date (UNIX timestamp)
     *
     * @return void
     */
    public function setUnsubscribeDate($timestamp)
    {
        $this->unsubscribeDate = $timestamp;
    }

    /**
     * Returns the date when the user unsubscribed.
     *
     * @return int
     */
    public function getUnsubscribeDate()
    {
        return $this->unsubscribeDate;
    }
}
