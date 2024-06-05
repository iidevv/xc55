<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\InputInterface;
use XLite\Base\Singleton;
use XcartGraphqlApi\Api as ApiFacade;

/**
 * Class Api
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation
 */
class Api extends Singleton
{
    /**
     * @var ApiFacade
     */
    protected $api;

    /**
     * Api constructor.
     */
    protected function __construct()
    {
        $this->initialize();

        $input   = new Input();
        $context = $this->buildContext($input);

        $this->api = new ApiFacade(
            $input,
            $context,
            new Output()
        );

        parent::__construct();
    }

    /**
     * @param InputInterface $input
     *
     * @return XCartContext
     */
    protected function buildContext(InputInterface $input)
    {
        $data    = $input->getData();
        $headers = $input->getHeaders();

        if (isset($data['variables']) && isset($data['variables']['context'])) {
            $lng = '';
            $cur = '';
            $jwt = '';

            extract($data['variables']['context']);

            $context = new XCartContext($lng, $cur, $jwt);
        } else if (isset($headers['X-App-Token'])
            || isset($headers['X-App-Currency'])
            || isset($headers['X-App-Language'])) {

            $jwt = isset($headers['X-App-Token']) ? $headers['X-App-Token'] : '';
            $cur = isset($headers['X-App-Currency']) ? $headers['X-App-Currency'] : '';
            $lng = isset($headers['X-App-Language']) ? $headers['X-App-Language'] : '';

            $context = new XCartContext($lng, $cur, $jwt);
        } else {
            $context = new XCartContext();
        }

        return $context;
    }

    protected function initialize()
    {
        Types::setResolverFactory(
            new ResolverFactory()
        );
    }

    public function start()
    {
        \XLite::initializeForMobileApi();

        $this->api->start();
    }
}
