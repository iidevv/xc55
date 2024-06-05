<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Resolver;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Mapper\ProductQuestion;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use XLite\Model\Product;

/**
 * Class AddReview
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

class UpdateQuestion  extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\UpdateQuestion
{
    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \GraphQL\Error\UserError
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        /** @var \QSL\ProductQuestions\Model\Question $question */
        $question = \XLite\Core\Database::getRepo("\QSL\ProductQuestions\Model\Question")
            ->find($args['id']);

        if (!$question) {
            throw new UserError("Can't find a question with {$args['id']} id");
        }

        $question->setAnswer($args['answer']);
        $question->setPrivate((bool) $args['private']);

        \XLite\Core\Database::getEM()->flush();

        return $this->mapQuestion($question);
    }

    /**
     * @param \QSL\ProductQuestions\Model\Question $question
     *
     * @return array
     */
    protected function mapQuestion($question)
    {
        $mapper = new ProductQuestion();

        return $mapper->mapToArray($question);
    }
}
