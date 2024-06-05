<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use QSL\Banner\Model\Banner;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

class BannerHtml implements ResolverInterface
{
    protected $banner;

    public function __construct(?Banner $banner = null)
    {
        $this->banner = $banner;
    }

    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $banners = [];

        /** @var \QSL\Banner\Model\Content[] $contents */
        $contents = $this->banner->getContents()->toArray();

        if ($contents) {
            foreach ($contents as $html) {
                $banners[] = [
                    'content' => $html->getContent()
                ];
            }
        }

        return $banners;
    }
}