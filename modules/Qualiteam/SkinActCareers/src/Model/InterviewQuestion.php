<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class Job
 * @ORM\Entity
 * @ORM\Table  (name="careers_interview_question")
 */
class InterviewQuestion extends \XLite\Model\AEntity
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={ "unsigned": true })
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $question;


    const TYPE_PLAIN = 'P';
    const TYPE_SELECT = 'S';
    const TYPE_TEXTAREA = 'T';

    /**
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     *
     * @var string
     */
    protected $type = self::TYPE_PLAIN;

    /**
     * @ORM\Column (type="boolean", options={ "default": false })
     *
     * @var bool
     */
    protected $mandatory = false;

    /**
     * @ORM\Column (type="boolean", options={ "default": true })
     *
     * @var bool
     */
    protected $enabled = true;


    /**
     * @ORM\Column (type="integer", options={ "default": 0 })
     *
     * @var int
     */
    protected $position = 0;


    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $predefinedValues;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $serviceName;

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }


    /**
     * @return string
     */
    public function getPredefinedValues()
    {
        return $this->predefinedValues;
    }

    /**
     * @param string $predefinedValues
     */
    public function setPredefinedValues($predefinedValues)
    {
        $this->predefinedValues = $predefinedValues;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @param bool $mandatory
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}