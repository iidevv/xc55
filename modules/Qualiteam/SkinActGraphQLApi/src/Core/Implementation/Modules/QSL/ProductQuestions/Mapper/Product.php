<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Mapper;

use Doctrine\Common\Collections\Collection;
use QSL\ProductQuestions\Model\Question;

/**
 * Class Cart
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\ProductQuestions")
 *
 */

class Product extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product
{
    /**
     * @param \XC\Reviews\Model\Product $product
     *
     * @return \XcartGraphqlApi\DTO\ProductDTO
     */
    public function mapToDto(\XLite\Model\Product $product, array $fields = [])
    {
        $dto = parent::mapToDto($product, $fields);

        $mapper = new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Mapper\ProductQuestion();

        $dto->questions = array_map(static function ($question) use ($mapper) {
            return $mapper->mapToArray($question);
        }, \XLite\Core\Database::getRepo(Question::class)
            ->findUserProductQuestions($product, \XLite\Core\Auth::getInstance()->getProfile())
        );

        $dto->questions = array_merge($dto->questions, array_map(static function ($question) use ($mapper) {
            return $mapper->mapToArray($question);
        }, \XLite\Core\Database::getRepo(Question::class)
            ->findOthersProductQuestions($product, \XLite\Core\Auth::getInstance()->getProfile())
        ));

        return $dto;
    }
}
