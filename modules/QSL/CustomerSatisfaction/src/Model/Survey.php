<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Survey
 *
 * @ORM\Entity (repositoryClass="\QSL\CustomerSatisfaction\Model\Repo\Survey")
 *
 * @ORM\Table  (name="surveys",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"id","order_id"})
 *      },
 *      indexes={
 *          @ORM\Index (name="status",     columns={"status"}),
 *          @ORM\Index (name="customer",   columns={"customer_id"}),
 *          @ORM\Index (name="emailDate", columns={"emailDate"}),
 *          @ORM\Index (name="rating",     columns={"rating"})
 *      }
 * )
 */
class Survey extends \XLite\Model\AEntity
{
    public const STATUS_HIDDEN      = 'H';
    public const STATUS_NEW         = 'N';
    public const STATUS_IN_PROGRESS = 'P';
    public const STATUS_CLOSED      = 'C';

    /**
     * Survey statuses labels
     *
     * @var array
     */
    protected $surveyStatuses = [
        \QSL\CustomerSatisfaction\Model\Survey::STATUS_NEW         => 'New',
        \QSL\CustomerSatisfaction\Model\Survey::STATUS_IN_PROGRESS => 'In progress',
        \QSL\CustomerSatisfaction\Model\Survey::STATUS_CLOSED      => 'Closed',
    ];

    /**
     * Unique survey ID
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Survey Order ID
     *
     * @var \XLite\Model\Order
     *
     * @ORM\OneToOne (targetEntity="XLite\Model\Order", inversedBy="survey")
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Survey rating
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $rating;

    /**
     * Survey processing status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status = \QSL\CustomerSatisfaction\Model\Survey::STATUS_HIDDEN;

    /**
     * Survey email sent date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $emailDate;

    /**
     * Survey init date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $initDate;

    /**
     * Survey feedback received date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $feedbackDate;

    /**
     * Survey feedback processed date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $feedbackProcessedDate;

    /**
     * Survey comments
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $comments = '';

    /**
     * Survey comments
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $customerMessage = '';

    /**
     * Survey assigned manager
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Profile", inversedBy="managerSurveys", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="manager_id", referencedColumnName="profile_id")
     */
    protected $manager;

    /**
     * Survey customer
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Profile", inversedBy="customerSurveys", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="customer_id", referencedColumnName="profile_id")
     */
    protected $customer;

    /**
     * Relations to a Answer entities
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\CustomerSatisfaction\Model\Answer", mappedBy="survey", cascade={"all"})
     */
    protected $answers;

    /**
     * Relation to a TagSurvey entities
     *
     * @var \QSL\CustomerSatisfaction\Model\Tag
     *
     * @ORM\ManyToMany (targetEntity="QSL\CustomerSatisfaction\Model\Tag", mappedBy="surveys", cascade={"all"})    *
     */
    protected $tags;

    /**
     * Hash key
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128, unique=true, nullable=true)
     */
    protected $hashKey;

    /**
     * Question is enabled
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $filled = false;

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @ORM\PrePersist
     */
    public function prepareBeforeCreate()
    {
        if (!$this->getInitDate()) {
            $this->setInitDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Return status string
     *
     * @return string
     *
     */
    public function getStatusString()
    {
        $statuses = $this->getSurveyStatuses();
        return $statuses[$this->getStatus()];
    }

    /**
     * Return tags in one string
     *
     * @return string
     *
     */
    public function getTagsString()
    {
        $tags = $this->tags;

        $returnTags = [];
        foreach ($tags as $tag) {
            $returnTags[] = $tag->getName();
        }
        if ($returnTags) {
            $tagsString = implode(',', $returnTags);
        }

        return (!empty($tagsString)) ? $tagsString : '';
    }


    /**
     * Return survey statuses
     *
     * @return array
     *
     */
    public static function getSurveyStatuses()
    {
        return [
           \QSL\CustomerSatisfaction\Model\Survey::STATUS_NEW          => 'New',
            \QSL\CustomerSatisfaction\Model\Survey::STATUS_IN_PROGRESS => 'In progress',
            \QSL\CustomerSatisfaction\Model\Survey::STATUS_CLOSED      => 'Closed',
        ];
    }

    /**
     * Sets the Survey statuses labels.
     *
     * @param array $surveyStatuses the survey statuses
     *
     * @return self
     */
    public function setSurveyStatuses(array $surveyStatuses)
    {
        $this->surveyStatuses = $surveyStatuses;
    }

    /**
     * Gets the Unique survey ID.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Unique survey ID.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the Survey Order ID.
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the Survey Order ID.
     *
     * @param \XLite\Model\Order $order the order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Gets the Survey rating.
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Sets the Survey rating.
     *
     * @param integer $rating the rating
     *
     * @return self
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * Gets the Survey processing status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the Survey processing status.
     *
     * @param string $status the status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Gets the Survey email sent date.
     *
     * @return integer
     */
    public function getEmailDate()
    {
        return $this->emailDate;
    }

    /**
     * Sets the Survey email sent date.
     *
     * @param integer $emailDate the email date
     *
     * @return self
     */
    public function setEmailDate($emailDate)
    {
        $this->emailDate = $emailDate;
    }

    /**
     * Gets the Survey init date.
     *
     * @return integer
     */
    public function getInitDate()
    {
        return $this->initDate;
    }

    /**
     * Sets the Survey init date.
     *
     * @param integer $initDate the init date
     *
     * @return self
     */
    public function setInitDate($initDate)
    {
        $this->initDate = $initDate;
    }

    /**
     * Gets the Survey feedback received date.
     *
     * @return integer
     */
    public function getFeedbackDate()
    {
        return $this->feedbackDate;
    }

    /**
     * Sets the Survey feedback received date.
     *
     * @param integer $feedbackDate the feedback date
     *
     * @return self
     */
    public function setFeedbackDate($feedbackDate)
    {
        $this->feedbackDate = $feedbackDate;
    }

    /**
     * Gets the Survey feedback processed date.
     *
     * @return integer
     */
    public function getFeedbackProcessedDate()
    {
        return $this->feedbackProcessedDate;
    }

    /**
     * Sets the Survey feedback processed date.
     *
     * @param integer $feedbackProcessedDate the feedback processed date
     *
     * @return self
     */
    public function setFeedbackProcessedDate($feedbackProcessedDate)
    {
        $this->feedbackProcessedDate = $feedbackProcessedDate;
    }

    /**
     * Gets the Survey comments.
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Sets the Survey comments.
     *
     * @param string $comments the comments
     *
     * @return self
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * Gets the Survey comments.
     *
     * @return string
     */
    public function getCustomerMessage()
    {
        return $this->customerMessage;
    }

    /**
     * Sets the Survey comments.
     *
     * @param string $customerMessage the customer message
     *
     * @return self
     */
    public function setCustomerMessage($customerMessage)
    {
        $this->customerMessage = $customerMessage;
    }

    /**
     * Gets the Survey assigned manager.
     *
     * @return \XLite\Model\Profile
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Sets the Survey assigned manager.
     *
     * @param \XLite\Model\Profile $manager the manager
     *
     * @return self
     */
    public function setManager(\XLite\Model\Profile $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets the Survey customer.
     *
     * @return \XLite\Model\Profile
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Sets the Survey customer.
     *
     * @param \XLite\Model\Profile $customer the customer
     *
     * @return self
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * Gets the Relations to a Answer entities.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Sets the Relations to a Answer entities.
     *
     * @param \Doctrine\Common\Collections\Collection $answers the answers
     *
     * @return self
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }

    /**
     * Gets the Relation to a TagSurvey entities.
     *
     * @return \QSL\CustomerSatisfaction\Model\Tag
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets the Relation to a TagSurvey entities.
     *
     * @param \QSL\CustomerSatisfaction\Model\Tag $tags the tags
     *
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Gets the Hash key.
     *
     * @return string
     */
    public function getHashKey()
    {
        return $this->hashKey;
    }

    /**
     * Sets the Hash key.
     *
     * @param string $hashKey the hash key
     *
     * @return self
     */
    public function setHashKey($hashKey)
    {
        $this->hashKey = $hashKey;
    }

    /**
     * Gets the Question is enabled.
     *
     * @return boolean
     */
    public function getFilled()
    {
        return $this->filled;
    }

    /**
     * Sets the Question is enabled.
     *
     * @param boolean $filled the filled
     *
     * @return self
     */
    public function setFilled($filled)
    {
        $this->filled = $filled;
    }
}
