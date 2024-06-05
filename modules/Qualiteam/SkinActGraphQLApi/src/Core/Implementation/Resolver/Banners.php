<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Includes\Utils\URLManager;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Request;
use XLite\Model\CleanURL;
use XLite\Model\BannerRotationSlide;

class Banners implements ResolverInterface
{
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
        $banners = array();

        /** @var \QSL\Banner\Model\BannerSlide[] $bannerSlides */
        $bannerSlides = \XLite\Core\Database::getRepo(\QSL\Banner\Model\BannerSlide::class)
            ->findBy([ 'enabled' => true ]);

        if ($bannerSlides) {
            foreach ($bannerSlides as $slide) {
                $url = static::parseBannerURL($slide->getLink());

                $banners[] = array(
                    'id'        => $slide->getId(),
                    'image_url' => $slide->getImage()->getURL(),
                    'type'      => $url['type'],
                    'data'      => $url['data'],
                );
            }
        }

        return $banners;
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
            'type'  => 'url',
            'data'  => $url,
        );

        $domains = URLManager::getShopDomains();
        $webDir = URLManager::getWebdir();

        $parsedUrl = parse_url($url);

        $isXcartUrl = false;
        foreach ($domains as $domain) {
            if (empty($parsedUrl['host']) || $parsedUrl['host'] == $domain) {
                $parsedUrl['path'] = str_replace( $webDir, '', $parsedUrl['path'] ?? '');
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
