<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Bridge\Symfony\Routing;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameGenerator;
use ApiPlatform\Core\Exception\InvalidResourceException;
use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class ApiLoader extends Loader
{
    public const DEFAULT_ACTION_PATTERN = 'api_platform.action.';

    private $fileLoader;
    private $resourceNameCollectionFactory;
    private $resourceMetadataFactory;
    private $operationPathResolver;
    private $container;
    private $formats;
    private $resourceClassDirectories;
    private $entrypointEnabled;
    private $docsEnabled;

    public function __construct(
        KernelInterface $kernel,
        ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
        OperationPathResolverInterface $operationPathResolver,
        ContainerInterface $container,
        array $formats,
        array $resourceClassDirectories = [],
        bool $entrypointEnabled = true,
        bool $docsEnabled = true
    ) {
        /** @var string[]|string $paths */
        $paths = $kernel->locateResource('@ApiPlatformBundle/Resources/config/routing');
        $this->fileLoader = new XmlFileLoader(new FileLocator($paths));
        $this->resourceNameCollectionFactory = $resourceNameCollectionFactory;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->operationPathResolver = $operationPathResolver;
        $this->container = $container;
        $this->formats = $formats;
        $this->resourceClassDirectories = $resourceClassDirectories;
        $this->entrypointEnabled = $entrypointEnabled;
        $this->docsEnabled = $docsEnabled;
    }

    public function load($data, $type = null): RouteCollection
    {
        $routeCollection = new RouteCollection();
        foreach ($this->resourceClassDirectories as $directory) {
            $routeCollection->addResource(new DirectoryResource($directory, '/\.php$/'));
        }

        $this->loadExternalFiles($routeCollection);

        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);
            $resourceShortName = $resourceMetadata->getShortName();

            if ($resourceShortName === null) {
                throw new InvalidResourceException(sprintf('Resource %s has no short name defined.', $resourceClass));
            }

            if (($collectionOperations = $resourceMetadata->getCollectionOperations()) !== null) {
                foreach ($collectionOperations as $operationName => $operation) {
                    $this->addRoute($routeCollection, $resourceClass, $operationName, $operation, $resourceMetadata, OperationType::COLLECTION);
                }
            }

            if (($itemOperations = $resourceMetadata->getItemOperations()) !== null) {
                foreach ($itemOperations as $operationName => $operation) {
                    $this->addRoute($routeCollection, $resourceClass, $operationName, $operation, $resourceMetadata, OperationType::ITEM);
                }
            }
        }

        return $routeCollection;
    }

    public function supports($resource, $type = null): bool
    {
        return $type === 'api_platform';
    }

    /**
     * Load external files.
     */
    private function loadExternalFiles(RouteCollection $routeCollection): void
    {
        if ($this->entrypointEnabled) {
            $routeCollection->addCollection($this->fileLoader->load('api.xml'));
        }

        if ($this->docsEnabled) {
            $routeCollection->addCollection($this->fileLoader->load('docs.xml'));
        }

        if (isset($this->formats['jsonld'])) {
            $routeCollection->addCollection($this->fileLoader->load('jsonld.xml'));
        }
    }

    /**
     * Creates and adds a route for the given operation to the route collection.
     *
     * @throws RuntimeException
     */
    private function addRoute(RouteCollection $routeCollection, string $resourceClass, string $operationName, array $operation, ResourceMetadata $resourceMetadata, string $operationType): void
    {
        $resourceShortName = $resourceMetadata->getShortName();

        if (isset($operation['route_name'])) {
            if (!isset($operation['method'])) {
                @trigger_error(sprintf('Not setting the "method" attribute is deprecated and will not be supported anymore in API Platform 3.0, set it for the %s operation "%s" of the class "%s".', $operationType === OperationType::COLLECTION ? 'collection' : 'item', $operationName, $resourceClass), \E_USER_DEPRECATED);
            }

            return;
        }

        if (!isset($operation['method'])) {
            throw new RuntimeException(sprintf('Either a "route_name" or a "method" operation attribute must exist for the operation "%s" of the resource "%s".', $operationName, $resourceClass));
        }

        if (($controller = $operation['controller'] ?? null) === null) {
            $controller = sprintf('%s%s_%s', self::DEFAULT_ACTION_PATTERN, strtolower($operation['method']), $operationType);

            if (!$this->container->has($controller)) {
                throw new RuntimeException(sprintf('There is no builtin action for the %s %s operation. You need to define the controller yourself.', $operationType, $operation['method']));
            }
        }

        if ($resourceMetadata->getItemOperations()) {
            if (!isset($operation['identifiers'])) {
                throw new RuntimeException(sprintf('Missing required "identifiers" option for "%s" %s operation on "%s" API resource', $operationName, $operationType, $resourceClass));
            }
        } else {
            $operation['identifiers'] = $operation['identifiers'] ?? [];
        }

        $operation['has_composite_identifier'] = \count($operation['identifiers']) > 1 ? $resourceMetadata->getAttribute('composite_identifier', true) : false;
        $path = trim(trim($resourceMetadata->getAttribute('route_prefix', '')), '/');
        $path .= $this->operationPathResolver->resolveOperationPath($resourceShortName, $operation, $operationType, $operationName);

        $route = new Route(
            $path,
            [
                '_controller' => $controller,
                '_format' => null,
                '_stateless' => $operation['stateless'],
                '_api_resource_class' => $resourceClass,
                '_api_identifiers' => $operation['identifiers'],
                '_api_has_composite_identifier' => $operation['has_composite_identifier'],
                sprintf('_api_%s_operation_name', $operationType) => $operationName,
            ] + ($operation['defaults'] ?? []),
            $operation['requirements'] ?? [],
            $operation['options'] ?? [],
            $operation['host'] ?? '',
            $operation['schemes'] ?? [],
            [$operation['method']],
            $operation['condition'] ?? ''
        );

        $routeCollection->add(RouteNameGenerator::generate($operationName, $resourceShortName, $operationType), $route);
    }
}
