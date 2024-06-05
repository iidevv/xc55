<?php

namespace Iidev\StripeSubscriptions\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * StripeSubscriptions
 *
 * @ORM\Entity
 * @ORM\Table(name="stripe_subscriptions")
 */
class StripeSubscriptions extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * Customer ID
     *
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $customerId;

    /**
     * Stripe Customer ID
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $stripeCustomerId;

    /**
     * Stripe Subscription ID
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $stripeSubscriptionId;

    /**
     * Expiration Date
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $expirationDate;

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * Periods
     *
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $periods;

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getStripeCustomerId(): string
    {
        return $this->stripeCustomerId;
    }

    public function getStripeSubscriptionId(): string
    {
        return $this->stripeSubscriptionId;
    }

    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPeriods(): int
    {
        return $this->periods;
    }

    // Setters
    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function setStripeCustomerId(string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;
        return $this;
    }

    public function setStripeSubscriptionId(string $stripeSubscriptionId): self
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        return $this;
    }

    public function setExpirationDate($expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setPeriods($periods): self
    {
        $this->periods = $periods;
        return $this;
    }
}
