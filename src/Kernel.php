<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use XCart\Event\BootEvent;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader): void
    {
        // Some params are shared between X-Cart and service-tool
        $container->import('../config/{shared}/*.yaml');

        // Symfony packages
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        // Dynamic configuration generated by service-tool
        $container->import('../config/{dynamic}/*.yaml');

        if (is_file(\dirname(__DIR__) . '/config/services.yaml')) {
            // Common X-Cart services
            $container->import('../config/services.yaml');
            $container->import('../config/services/*.yaml');
            $container->import('../config/{services}_' . $this->environment . '.yaml');

            // API related config
            $container->import('../config/services/api/*.yaml');
            $container->import('../config/services/api/' . $this->environment . '*.yaml');
        } else {
            $container->import('../config/{services}.php');
        }

        // Modules configuration
        $loader->load(function (ContainerBuilder $containerBuilder) use ($container) {
            foreach ($this->getBundles() as $bundle) {
                if (strpos($bundle->getPath(), 'var/run/classes') !== false) {
                    $path = str_replace('var/run/classes', 'modules', $bundle->getPath());

                    $container->import($path . '/{config}/services.yaml');
                } elseif (strpos($bundle->getPath(), 'modules/') !== false) {
                    $container->import($bundle->getPath() . '/../{config}/services.yaml');
                }
            }
        });

        // Load from local config files to avoid editing dist configuration
        $container->import('../config/{local}/shared/*.yaml');
        $container->import('../config/{local}/*.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } else {
            $routes->import('../config/{routes}.php');
        }
    }

    /**
     * Returns an array of bundles to register.
     *
     * @return iterable|BundleInterface[] An iterable of bundle instances
     */
    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }

        $contents = require $this->getProjectDir() . '/config/dynamic/xcart_bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        parent::boot();

        Container::setContainer($this->getContainer());

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');
        $eventDispatcher->dispatch(new BootEvent(), 'xcart.boot');
    }
}
