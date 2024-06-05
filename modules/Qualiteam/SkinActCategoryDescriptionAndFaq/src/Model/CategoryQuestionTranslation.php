<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category Question multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="category_questions_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class CategoryQuestionTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Question
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $question = '';

    /**
     * Answer
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $answer = '';

    /**
     * @var \Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set question
     *
     * @param string $question
     * @return CategoryQuestionTranslation
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
     * Set answer
     *
     * @param string $answer
     * @return CategoryQuestionTranslation
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return CategoryQuestionTranslation
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
