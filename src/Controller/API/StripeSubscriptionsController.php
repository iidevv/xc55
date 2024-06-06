<?php

namespace XCart\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Iidev\StripeSubscriptions\Core\Session;
use \XLite\Core\Config;
use Iidev\StripeSubscriptions\Core\HookManager;
use XLite\Model\Profile;

final class StripeSubscriptionsController extends AbstractController
{
    private $hookManager;

    public function __construct()
    {
        $this->hookManager = new HookManager();
    }
    /**
     * @Route(path="/stripe-webhook", name="stripe-webhook", methods={"POST"})
     * @return Response
     */
    public function index(Request $request): Response
    {
        $event = null;
        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        $webhook_secret = Config::getInstance()->Iidev->StripeSubscriptions->webhook_secret;

        \Stripe\Stripe::setApiKey(Config::getInstance()->Iidev->StripeSubscriptions->secret_key);

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $webhook_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        $decodedPayload = json_decode($payload, true);
        $stripeData = $decodedPayload['data']['object'];

        $this->hookManager->handleSubscription($event->type, $stripeData);

        http_response_code(200);

        return $this->json($request);
    }

    /**
     * @Route(path="/stripe-subscriptions", name="stripe-subscriptions", methods={"POST"})
     * @return Response
     */
    public function subscriptions(Request $request): Response
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        $returnUrl = $request->request->get('return_url');
        $successUrl = $request->request->get('success_url');

        if (!empty($profile) && !empty($returnUrl)) {
            $session = (new Session($profile, $returnUrl, $successUrl))->createSession();
            header("HTTP/1.1 303 See Other");
            header("Location: " . $session->url);
            exit();
        } else {
            return $this->json(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

    }

    /**
     * @Route(path="/stripe-account", name="stripe-account", methods={"POST"})
     * @return Response
     */
    public function customerPortal(Request $request): Response
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        $returnUrl = $request->request->get('return_url');

        if (!empty($profile) && !empty($returnUrl)) {
            $session = (new Session($profile, $returnUrl))->createAccountSession();

            if (!$session) {
                \XLite\Core\TopMessage::addError('Profile not found. Please contact support.');
                header("HTTP/1.1 303 See Other");
                header("Location: " . $returnUrl);
                exit();
            }

            header("HTTP/1.1 303 See Other");
            header("Location: " . $session->url);
            exit();
        } else {
            return $this->json(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

    }

    /**
     * @Route(path="/stripe-membership-status", name="stripe-membership-status", methods={"GET"})
     * @return Response
     */
    public function membershipStatus(Request $request): Response
    {
        /** @var Profile $profile */
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        if ($profile && $profile->getMembership()) {
            return $this->json(
                [
                    'message' => true,
                ]
            );
        } elseif ($profile) {
            return $this->json(
                [
                    'message' => false,
                ]
            );
        } else {
            return $this->json(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

    }
}
