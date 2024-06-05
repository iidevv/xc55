<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Product questions
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\ProductQuestions\Model\Question", mappedBy="product", cascade={"all"})
     * @ORM\OrderBy   ({"date" = "DESC"})
     */
    protected $questions;

    /**
     * Adds a question about the product.
     *
     * @param \QSL\ProductQuestions\Model\Question $questions
     *
     * @return Product
     */
    public function addQuestions(\QSL\ProductQuestions\Model\Question $questions)
    {
        $this->questions[] = $questions;

        return $this;
    }

    /**
     * Returns questions about the product.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
