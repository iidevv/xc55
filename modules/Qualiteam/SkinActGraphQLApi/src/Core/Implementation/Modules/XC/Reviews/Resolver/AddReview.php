<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\Reviews\Resolver;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\Reviews\Mapper\Review;
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
 * @Extender\Depend("XC\Reviews")
 *
 */

class AddReview  extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\AddReview
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
        return $this->createReview(
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
    public function createReview($profile, $params)
    {
        /** @var \XLite\Model\Product $product */
        $product = \XLite\Core\Database::getRepo(Product::class)
            ->find($params['product_id']);
        if (!$product) {
            throw new UserError("Can't find product with {$params['product_id']} id");
        }

        $review = new \XC\Reviews\Model\Review();

        \XLite\Core\Database::getEM()->persist($review);

        $review->setProduct($product);

        if ($profile) {
            $review->setProfile($profile);
        }

        isset($params['rating']) ? $review->setRating($params['rating']) : null;

        $review->setReview($params['review']);
        $review->setReviewerName($params['name']);

        \XLite\Core\Database::getEM()->flush();

        return $this->mapReview($review);
    }

    /**
     * @param \XC\Reviews\Model\Review $review
     *
     * @return array
     */
    protected function mapReview($review)
    {
        $mapper = new Review();

        return $mapper->mapToArray($review);
    }
}
