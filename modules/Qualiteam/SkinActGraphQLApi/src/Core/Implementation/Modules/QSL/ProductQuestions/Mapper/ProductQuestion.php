<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Mapper;

class ProductQuestion
{
    /**
     * @param \QSL\ProductQuestions\Model\Question $question
     *
     * @return array
     */
    public function mapToArray(\QSL\ProductQuestions\Model\Question $question)
    {
        return [
            'id'            => $question->getId(),
            'question'      => $question->getQuestion(),
            'questionName'  => $question->getProfile() ? $question->getProfile()->getName() : $question->getName(),
            'questionDate'  => \XLite\Core\Converter::formatTime($question->getDate()),
            'answer'        => $question->getAnswer(),
            'answerName'    => $question->getAnswerProfile() ? $question->getAnswerProfile()->getName() : 'Customer',
            'answerDate'    => \XLite\Core\Converter::formatTime($question->getAnswerDate()),
            'private'       => $question->getPrivate(),
            'productName'   => $question->getProduct() ? $question->getProduct()->getName() : '',
        ];
    }
}
