<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XLite\Core\CommonCell;
use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ProductQuestions\Mapper\ProductQuestion;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\ProductQuestions")
 *
 */

class Questions  extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Questions
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
        $profile = $this->getProfile($val, $context) ;

        $mapper = new ProductQuestion();

        return array_map(static function ($item) use ($mapper) {
            return $mapper->mapToArray($item);
        }, $this->getQuestions($profile, $args));
    }

    /**
     * @return array
     */
    protected function getQuestions(Profile $profile, $params)
    {
        $cnd = new CommonCell();
        $cnd->{\QSL\ProductQuestions\Model\Repo\Question::SEARCH_VENDOR_ID} = $profile->getProfileId();
        $cnd->{\QSL\ProductQuestions\Model\Repo\Question::P_LIMIT} = [$params['start'], $params['limit']];

        return \XLite\Core\Database::getRepo('\QSL\ProductQuestions\Model\Question')->search($cnd);
    }

    /**
     * @param $val
     * @param $args
     */
    protected function getProfile($val, $context)
    {
        if (isset($val['id'])) {
            return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($val['id']);
        }

        return $context->getLoggedProfile();
    }
}
