<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;

/**
 * Category question
 *
 * @ORM\Entity
 * @ORM\Table  (name="category_questions")
 */
class CategoryQuestion extends \XLite\Model\Base\I18n
{
    /**
     * Node unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Node status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Category Question position parameter
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestionTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return CategoryQuestion
     */
    public function setEnabled($enabled)
    {
        $this->getPreviousState()->enabled = $this->enabled;
        $this->enabled                     = (bool)$enabled;

        return $this;
    }

    /**
     * Set Position
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Return Position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    // {{{ Translation Getters / setters
    /**
     * @param string $question
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setQuestion($question)
    {
        return $this->setTranslationField(__FUNCTION__, $question);
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $answer
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setAnswer($answer)
    {
        return $this->setTranslationField(__FUNCTION__, $answer);
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->getTranslationField(__FUNCTION__);
    }
    // }}}
}
