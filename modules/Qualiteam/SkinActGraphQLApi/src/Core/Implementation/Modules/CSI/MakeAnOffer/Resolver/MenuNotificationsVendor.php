<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XLite\Core\CommonCell;
use XLite\Model\Order;
use CSI\MakeAnOffer\Model\MakeAnOffer;
use QSL\ProductQuestions\Model\Question;

/**
 * Class MenuNotificationsVendor
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Resolver
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend({"CSI\MakeAnOffer", "QSL\ProductQuestions"})
 *
 */

class MenuNotificationsVendor extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\MenuNotificationsVendor
{
    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $profile = $context->getLoggedProfile();

        return $this->prepareResult($context, [
            'offers'        => $profile ? $this->getOffersCount($profile) : 0,
            'questions'     => $profile ? $this->getQuestionsCount($profile) : 0,
        ]);
    }

    /**
     * @return mixed
     */
    protected function getOffersCount($profile)
    {
        $cnd = new CommonCell();
        $cnd->{\CSI\MakeAnOffer\Model\Repo\MakeAnOffer::SEARCH_STATUS} = 'P';
        $cnd->{\CSI\MakeAnOffer\Model\Repo\MakeAnOffer::SEARCH_PROFILE} = $profile;

        return \XLite\Core\Database::getRepo(MakeAnOffer::class)->search($cnd, true);
    }

    /**
     * @param $profile
     * @return mixed
     */
    protected function getQuestionsCount($profile)
    {
        return \XLite\Core\Database::getRepo(Question::class)
            ->searchUnansweredVendorQuestions(true, $profile);
    }
}