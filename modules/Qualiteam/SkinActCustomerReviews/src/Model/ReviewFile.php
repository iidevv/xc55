<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\Model;


use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Config;

/**
 * Content images file storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="review_files")
 */
class ReviewFile extends \XLite\Model\Base\Image
{
    /**
     * Relation to a product entity
     *
     * @var \XC\Reviews\Model\Review
     *
     * @ORM\ManyToOne  (targetEntity="XC\Reviews\Model\Review", inversedBy="files")
     * @ORM\JoinColumn (name="review_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $review;

    /**
     * @return \XC\Reviews\Model\Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param \XC\Reviews\Model\Review $review
     */
    public function setReview($review)
    {
        $this->review = $review;
    }

    protected function renewByPath($path)
    {
        if (($data = $this->getSystemImageData($path))
            && is_array($data)
            && count($data) > 0
        ) {
            return parent::renewByPath($path);
        }

        $hash = \Includes\Utils\FileManager::getHash($path, false, $this->includeFilenameInHash);

        if ($hash) {
            $this->setHash($hash);
        }

        return true;
    }

    public function getExtensionByMIME()
    {
        $ext = parent::getExtensionByMIME();

        if (empty($ext) && Config::getInstance()->XC->Reviews->allow_upload_videos) {
            return pathinfo($this->getFileName(), PATHINFO_EXTENSION);
        }

        return $ext;
    }

    public function getFrontURL()
    {
        return \XLite\Core\Converter::makeURLValid($this->getURL());
    }

    public function loadFromLocalFile($path, $basename = null, $makeUnique = false)
    {
        return parent::loadFromLocalFile($path, $basename, true);
    }
}
