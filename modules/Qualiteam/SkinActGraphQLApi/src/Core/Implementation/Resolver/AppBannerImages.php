<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Includes\Utils\URLManager;
use QSL\Banner\Model\Banner;
use QSL\Banner\Model\BannerSlide;
use QSL\Banner\Model\Repo\BannerSlide as BannerSlideRepo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\CleanURL;

class AppBannerImages implements ResolverInterface
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

        /** @var \QSL\Banner\Model\BannerSlide[] $bannerSlides */
        $bannerSlides = $this->getBannerSlides($args);

        $allowedLocations = [
            'StandardTop' => 1,
            'StandardMiddle' => 2,
            'StandardBottom' => 3
        ];

        if ($bannerSlides) {
            $secondPosAdded = false;
            foreach ($bannerSlides as $slide) {
                if ($slide->getImage()
                    || $slide->getLink()
                ) {

                    if (!array_key_exists($this->banner->getLocation(), $allowedLocations)) {
                        continue;
                    }

                    if ($this->banner->getLocation() === 'StandardMiddle'
                        && $secondPosAdded
                    ) {
                        continue;
                    }

                    if ($this->banner->getLocation() === 'StandardMiddle') {
                        $secondPosAdded = true;
                    }

                    $banners[] = [
                        'image_url' => $slide->getImage() ? $slide->getImage()->getURL() : '',
                        'banner_url' => $slide->getLink(),
                    ];
                }
            }
        }

        return $banners;
    }

    protected function getBannerSlides($args)
    {
        $repo = Database::getRepo(BannerSlide::class);
        $cnd = $this->prepareSearchCaseBySearchParams($args, $repo->getDefaultAlias());

        return $repo->search($cnd);

    }

    protected function prepareSearchCaseBySearchParams($args, $mainAlias)
    {
        $cnd = new CommonCell();

        $cnd->{BannerSlideRepo::P_ENABLED} = true;
        $cnd->{BannerSlideRepo::P_ORDER_BY} = [
            $this->prepareOrderByMobilePosition($mainAlias),
            'ASC',
        ];
        $cnd->{BannerSlideRepo::SEARCH_PARAM_BANNER} = $this->banner;

        return $cnd;
    }

    protected function prepareOrderByMobilePosition(string $mainAlias): string
    {
        return sprintf('%s.%s',
            $mainAlias,
            'position'
        );
    }

    /**
     * Parse banner URL
     *
     * @param string $url URL
     *
     * @return array
     */
    protected static function parseBannerURL($url)
    {
        $return = array(
            'type' => 'url',
            'data' => $url,
        );

        $domains = URLManager::getShopDomains();
        $webDir = URLManager::getWebdir();

        $parsedUrl = parse_url($url);

        $isXcartUrl = false;
        foreach ($domains as $domain) {
            if (empty($parsedUrl['host']) || $parsedUrl['host'] == $domain) {
                $parsedUrl['path'] = str_replace($webDir, '', $parsedUrl['path'] ?? '');
                $parsedUrl['path'] = ltrim($parsedUrl['path'], '/');

                $isXcartUrl = true;
                break;
            }
        }

        if ($isXcartUrl) {
            Request::getInstance()->url = $parsedUrl['path'];
            Request::getInstance()->last = null;
            Request::getInstance()->rest = null;
            Request::getInstance()->ext = null;

            \XLite\Core\Router::getInstance()->processCleanUrls();

            $urlContents = \XLite\Core\Database::getRepo(CleanURL::class)->parseURL(
                Request::getInstance()->url,
                Request::getInstance()->last,
                Request::getInstance()->rest,
                Request::getInstance()->ext
            );

            if ($urlContents[0]) {
                $return['type'] = $urlContents[0];

                switch ($return['type']) {
                    case 'category':
                        $return['data'] = $urlContents[1]['category_id'];
                        break;
                    case 'product':
                        $return['data'] = $urlContents[1]['product_id'];
                        break;
                }
            }
        }

        return $return;
    }
}