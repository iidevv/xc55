<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * The "question" model class
 *
 * @ORM\Entity (repositoryClass="\QSL\ProductQuestions\Model\Repo\Question")
 * @ORM\Table (
 *     name="product_questions",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint (name="id", columns={"id"})
 *     },
 *     indexes={
 *         @ORM\Index (name="date", columns={"date"}),
 *         @ORM\Index (name="published", columns={"published"}),
 *         @ORM\Index (name="private", columns={"private"}),
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Question extends \XLite\Model\AEntity
{
    /**
     * Question identifier.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Question text
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $question = '';

    /**
     * Answer text
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $answer = '';

    /**
     * Date the question was asked (UNIX timestamp)
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date = 0;

    /**
     * Date the question was answered (UNIX timestamp)
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $answerDate = 0;

    /**
     * Relation to a profile entity (who asked the question)
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile", inversedBy="questions")
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="SET NULL")
     */
    protected $profile;

    /**
     * Relation to a profile entity (who answered the question)
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile")
     * @ORM\JoinColumn (name="answer_profile_id", referencedColumnName="profile_id", onDelete="SET NULL")
     */
    protected $answerProfile;

    /**
     * Name of the customer asked the question.
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name = '';

    /**
     * E-mail of the customer asked the question.
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $email = '';

    /**
     * Whether the question is published, or being moderated.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $published = false;

    /**
     * Whether the question is a private one, or a public one.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $private = false;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="questions")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Check if the question is a new one.
     *
     * @return boolean
     */
    public function isNew()
    {
        return !$this->isPersistent();
    }

    /**
     * Check if the question has answer.
     *
     * @return boolean
     */
    public function isReplied()
    {
        return trim($this->getAnswer()) && $this->getPublished();
    }

    /**
     * Check if the question is a public one.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return !$this->getPrivate();
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @ORM\PrePersist
     */
    public function prepareBeforeCreate()
    {
        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Update the "published" flag.
     *
     * @return void
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        $this->setPublished(trim($this->getAnswer()) !== '');

        if ($this->getPublished()) {
            $this->setAnswerDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Returns the question identifier.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Updates the question text.
     *
     * @param string $question New text
     *
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Returns the question text.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Updates the question answer.
     *
     * @param string $answer New answer
     *
     * @return Question
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Returns the question answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Updates the question date.
     *
     * @param integer $date Date (timestamp)
     *
     * @return Question
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Returns the question date (timestamp).
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Updates the answer date.
     *
     * @param integer $answerDate Date (timestamp)
     * @return Question
     */
    public function setAnswerDate($answerDate)
    {
        $this->answerDate = $answerDate;
        return $this;
    }

    /**
     * Returns the answer date.
     *
     * @return integer
     */
    public function getAnswerDate()
    {
        return $this->answerDate;
    }

    /**
     * Updates the name of the user asked the question.
     *
     * @param string $name Full name
     *
     * @return Question
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the name of the user asked the question.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Updates the e-mail of the user asked the question.
     *
     * @param string $email E-mail
     *
     * @return Question
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Returns the e-mail of the user asked the question.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Updates the "published" flag.
     *
     * @param boolean $published New flag state
     *
     * @return Question
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Returns the "published" flag.
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Sets the "private question" flag.
     *
     * @param boolean $private New flag state
     *
     * @return Question
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Returns the "private question" flag.
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Associates a user profile with the question.
     *
     * @param \XLite\Model\Profile $profile User profile
     *
     * @return Question
     */
    public function setProfile(\XLite\Model\Profile $profile = null)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Returns the user profile associated with the question.
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Associates the question with the user that answered it.
     *
     * @param \XLite\Model\Profile $answerProfile User profile
     *
     * @return Question
     */
    public function setAnswerProfile(\XLite\Model\Profile $answerProfile = null)
    {
        $this->answerProfile = $answerProfile;

        return $this;
    }

    /**
     * Returns the profile of the user that answered the question.
     *
     * @return \XLite\Model\Profile
     */
    public function getAnswerProfile()
    {
        return $this->answerProfile;
    }

    /**
     * Associates the question with a product.
     *
     * @param \XLite\Model\Product $product Product model
     *
     * @return Question
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Returns the product that is associated with the question.
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
