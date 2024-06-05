<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use QSL\Banner\Model\Banner;
use QSL\Banner\Model\Repo\Banner as BannerRepo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Category;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\CommonCell;
use XLite\Core\Database;

class BannersList implements ResolverInterface
{
    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $banners = [];
        $model = $this->getBanners($args, $context);

        $seenLocations = [];

        $locationsMap = [
            'StandardTop' => 1,
            'StandardMiddle' => 2,
            'StandardBottom' => 3
        ];

        /** @var Banner $item */
        foreach ($model as $item) {
            if (isset($seenLocations[$item->getLocation()])) {
                continue;
            }
            $seenLocations[$item->getLocation()] = 1;
            $linksList = $this->prepareBannerImages($item)($val, $args, $context, $info);
            if ($linksList) {

                $position = $locationsMap[$item->getLocation()];

                $banners[] = [
                    'categories' => $this->prepareCategories($item),
                    'linksList' => $this->prepareBannerImages($item),
                    'banner_position' => $position,
                ];
            }

        }

        return $banners;
    }

    protected function getBanners($args, $context)
    {
        $repo = Database::getRepo(Banner::class);
        $cnd = $this->prepareSearchCaseBySearchParams($args, $repo->getDefaultAlias(), $context);

        return $repo->search($cnd);
    }

    protected function prepareSearchCaseBySearchParams($args, $mainAlias, $context)
    {
        $cnd = new CommonCell();

        $cnd->for_mobile_only = true;

        if ($context->getLoggedProfile()
            && $context->getLoggedProfile()->getMembershipId() > 0
        ) {
            $cnd->membership_id = $context->getLoggedProfile()->getMembershipId();
        } else {
            $cnd->membership_id = 0;
            $cnd->no_membership = true;
        }

        $cnd->{BannerRepo::P_ENABLED} = true;
        $cnd->{BannerRepo::P_ORDER_BY} = [
            $this->prepareOrderByMobilePosition($mainAlias),
            'ASC',
        ];

        $category_id = isset($args['category_id'])
            ? (int)$args['category_id']
            : null;

//        if ($category_id) {
//            $cnd->{BannerRepo::P_CATEGORY_ID} = $category_id;
//        }

        $from = isset($args['from'])
            ? (int)$args['from']
            : 0;
        $size = isset($args['size'])
            ? (int)$args['size']
            : 0;

        if ($from || $size) {
            $cnd->{BannerRepo::P_LIMIT} = [$from, $size];
        }

        return $cnd;
    }

    protected function prepareOrderByMobilePosition(string $mainAlias): string
    {
        return sprintf('%s.%s',
            $mainAlias,
            'position'
        );
    }

    protected function getId(Banner $item)
    {
        return $item->getId();
    }

    protected function getTitleEscaped(Banner $item)
    {
        return $item->getTitleEscaped();
    }

    protected function getLocation(Banner $item)
    {
        return $item->getLocation();
    }

    protected function prepareCategories(Banner $item)
    {
        $categories = [];

        foreach ($item->getCategories() as $category) {
            $categories[] = (new Category())->mapToDto($category);
        }

        return $categories;
    }

    protected function prepareBannerImages(Banner $item)
    {
        return new AppBannerImages($item);
    }

    protected function prepareBannerHtml(Banner $item)
    {
        return new BannerHtml($item);
    }
}