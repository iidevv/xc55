<?php


namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User;


use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Database;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;

class ConvertToVendor implements ResolverInterface
{

    /**
     * @var \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\User
     */
    private $mapper;
    /**
     * @var \Qualiteam\SkinActGraphQLApi\Core\CommonRoutines
     */
    private $routines;

    public function __construct(\Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\User $mapper)
    {
        $this->mapper = $mapper;
        $this->routines = \Qualiteam\SkinActGraphQLApi\Core\CommonRoutines::getInstance();
    }

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $this->validate($args, $context);

        $this->routines->preprocessArgs($args);

        $profile = $this->routines->makeVendor($args, $context, 'convert');

        return $this->mapper->mapToDto($profile, $context);
    }


    protected function validate($args, ContextInterface $context)
    {
        $profile = $context->getLoggedProfile();

        if (!$profile) {
            $this->routines->exception('You must be logged in for "user to vendor" convertation');
        }

        if ($profile->isVendor()) {
            $this->routines->exception('You are already a vendor');
        }

        $this->routines->validateVendorPlanId($args);

    }
}