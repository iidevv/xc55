<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\Guru\FAQ\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class FAQ
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("Guru\FAQ")
 *
 */

class FAQ extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\FAQ
{
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $result = [];

        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\Guru\FAQ\Model\Repo\FAQ::SEARCH_ENABLED} = true;

        /** @var \Guru\FAQ\Model\FAQ[] $faq */
        $faq = \XLite\Core\Database::getRepo('\Guru\FAQ\Model\FAQ')->search($cnd);

        if ($faq) {
            foreach ($faq as $i => $question) {
                $result[] = [
                    'question'  => $question->getQuestion(),
                    'answer'    => $question->getAnswer(),
                ];
            }
        }

        return $result;
    }
}
