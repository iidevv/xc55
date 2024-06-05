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

class AddQuestion  extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\AddQuestion
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
        return $this->createQuestion(
            $context->getLoggedProfile(),
            $args
        );
    }


    /**
     * @param $profile
     * @param $params
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \GraphQL\Error\UserError
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createQuestion($profile, $params)
    {
        /** @var \XLite\Model\Product $product */
        $product = \XLite\Core\Database::getRepo(Product::class)
            ->find($params['product_id']);
        if (!$product) {
            throw new UserError("Can't find product with {$params['product_id']} id");
        }

        $question = new \QSL\ProductQuestions\Model\Question();

        \XLite\Core\Database::getEM()->persist($question);

        $question->setProduct($product);

        if ($profile) {
            $question->setProfile($profile);
        }

        $question->setQuestion($params['question']);
        $question->setName($params['name']);

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
