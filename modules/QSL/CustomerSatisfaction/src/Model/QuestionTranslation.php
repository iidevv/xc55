<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tax multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (name="survey_question_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"})
 *         }
 * )
 */
class QuestionTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Question
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $question;

    /**
     * @var \QSL\CustomerSatisfaction\Model\Question
     *
     * @ORM\ManyToOne (targetEntity="QSL\CustomerSatisfaction\Model\Question", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set question
     *
     * @param string $question
     * @return QuestionTranslation
     */
    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Get label_id
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return QuestionTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
