<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (
 *     name="answers",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint (name="pair", columns={"survey_id","question_id"})
 *     }
 * )
 */
class Answer extends \XLite\Model\AEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", nullable=false)
     */
    protected $id;

    /**
     * Survey answer for question
     *
     * @var int
     *
     * @ORM\Column (type="integer", length=1, nullable=false)
     */
    protected $value = 0;

    /**
     * Survey answer for question
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $originQuestion = '';

    /**
     * Relation to a survey entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne (targetEntity="QSL\CustomerSatisfaction\Model\Survey", inversedBy="answers")
     * @ORM\JoinColumn (name="survey_id", referencedColumnName="id"  )
     */
    protected $survey;

    /**
     * Relation to a question entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne (targetEntity="QSL\CustomerSatisfaction\Model\Question", inversedBy="answers")
     * @ORM\JoinColumn (name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * Gets the Primary key.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Primary key.
     *
     * @param integer $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the Survey answer for question.
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the Survey answer for question.
     *
     * @param integer $value the value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Gets the Survey answer for question.
     *
     * @return string
     */
    public function getOriginQuestion()
    {
        return $this->originQuestion;
    }

    /**
     * Sets the Survey answer for question.
     *
     * @param string $originQuestion the origin question
     *
     * @return self
     */
    public function setOriginQuestion($originQuestion)
    {
        $this->originQuestion = $originQuestion;
    }

    /**
     * Gets the Relation to a survey entity.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Sets the Relation to a survey entity.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $survey the survey
     *
     * @return self
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * Gets the Relation to a question entity.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets the Relation to a question entity.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $question the question
     *
     * @return self
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }
}
