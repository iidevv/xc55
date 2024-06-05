<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language label
 *
 * @ORM\Entity
 * @ORM\Table (name="language_labels",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="name", columns={"name"})
 *      }
 * )
 */
class LanguageLabel extends \XLite\Model\Base\I18n
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer")
     */
    protected $label_id;

    /**
     * Label name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, options={"collation":"utf8mb4_bin"})
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\LanguageLabelTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get label translation
     *
     * @param string $code Language code OPTIONAL
     *
     * @return \XLite\Model\LanguageLabelTranslation
     */
    public function getLabelTranslation($code = null)
    {
        $result = null;

        $query = \XLite\Core\Translation::getLanguageQuery($code);
        foreach ($query as $code) {
            $result = $this->getTranslation($code, true);
            if (isset($result) || $code == 'en') {
                break;
            }
        }

        return $result;
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
     * Set name
     *
     * @param string $name
     * @return LanguageLabel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel($label)
    {
        $this->setTranslationField(__FUNCTION__, $label);
    }
}
