<?php


namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User;


use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Database;
use XLite\Core\Session;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;

class RegisterVendor implements ResolverInterface
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

        $profile = $this->routines->makeVendor($args, $context);

        $token = $context->getAuthService()->generateToken($profile);

        return $this->mapper->mapToDto($profile, $context, $token);
    }


    protected function validate($args, $context)
    {
        $loggedProfile = $context->getLoggedProfile();

        if ($loggedProfile) {
            $this->routines->exception('You already logged in. Use "convertToVendor" mutation instead of this one.');
        }

        $login = $args['login'] ?? null;

        if (!$login) {
            $this->routines->exception('Login cannot be empty');
        }

        $profile = Database::getRepo('XLite\Model\Profile')->findOneBy(['login' => $login]);

        if ($profile) {
            $this->routines->exception('User with such login already exists');
        }

        $this->routines->validateVendorPlanId($args);

    }
}

