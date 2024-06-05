<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class ReviewType
 * @package XcartGraphqlApi\Types\Model
 */
class QuestionType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'question',
            'description' => 'Question information model',
            'fields'      => function () {
                return [
                    'id'            => Types::id(),
                    'question'      => Types::string(),
                    'questionName'  => Types::string(),
                    'questionDate'  => Types::string(),
                    'answer'        => Types::string(),
                    'answerName'    => Types::string(),
                    'answerDate'    => Types::string(),
                    'private'       => Types::boolean(),
                    'productName'   => Types::string(),
                ];
            },
        ];
    }
}
