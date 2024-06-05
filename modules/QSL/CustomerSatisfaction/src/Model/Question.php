<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Entity (repositoryClass="\QSL\CustomerSatisfaction\Model\Repo\Question")
 *
 * @ORM\Table  (name="questions",
 *      indexes={
 *          @ORM\Index (name="position",     columns={"position"}),
 *          @ORM\Index (name="enabled",    columns={"enabled"})
 *      }
 * )
 * @ORM\MappedSuperclass
 */
class Question extends \XLite\Model\Base\I18n
{
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
     * Relation to a Answer entities
     *
     * @var \QSL\CustomerSatisfaction\Model\Answer
     *
     * @ORM\OneToMany (targetEntity="QSL\CustomerSatisfaction\Model\Answer", mappedBy="question", cascade={"all"})     *
     */
    protected $answers;

    /**
     * Question order
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Question text
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $question = '';

    /**
     * Question is enabled
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\CustomerSatisfaction\Model\QuestionTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

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

        return $this;
    }

    /**
     * Set question
     *
     * @param string $question
     * @return QuestionTranslation
     */
    public function setDefaultQuestion($question)
    {
        $this->question = $question;
        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getDefaultQuestion()
    {
        return $this->question;
    }

    /**
     * Gets the Relation to a Answer entities.
     *
     * @return \QSL\CustomerSatisfaction\Model\Answer
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Sets the Relation to a Answer entities.
     *
     * @param \QSL\CustomerSatisfaction\Model\Answer $answers the answers
     *
     * @return self
     */
    public function setAnswers(\QSL\CustomerSatisfaction\Model\Answer $answers)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Gets the Question order.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the Question order.
     *
     * @param integer $position the position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Gets the Question is enabled.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the Question is enabled.
     *
     * @param boolean $enabled the enabled
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $question
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setQuestion($question)
    {
        return $this->setTranslationField(__FUNCTION__, $question);
    }

    // }}}
}
