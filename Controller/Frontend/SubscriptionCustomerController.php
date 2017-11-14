<?php

/*
 * This file is part of the Mobile Cart package.
 *
 * (c) Jesse Hanson <jesse@mobilecart.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MobileCart\SubscriptionBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Constants\EntityConstants as CoreEntityConstants;
use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Event\SubscriptionEvents;

/**
 * Class SubscriptionCustomerController
 * @package MobileCart\SubscriptionBundle\Controller\Frontend
 */
class SubscriptionCustomerController extends Controller
{
    /**
     * @var string
     */
    protected $objectType = EntityConstants::SUBSCRIPTION_CUSTOMER;

    public function indexAction(Request $request)
    {
        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setRequest($request)
            ->setUser($this->getUser())
            ->setSection(CoreEvent::SECTION_FRONTEND);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_FRONTEND_LIST, $event);

        return $event->getResponse();
    }

    public function addAction(Request $request)
    {
        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setSection(CoreEvent::SECTION_FRONTEND)
            ->setEntity($this->get('cart.entity')->getInstance(CoreEntityConstants::CUSTOMER))
            ->setRequest($request)
            ->setUser($this->getUser())
            ->setFormAction($this->generateUrl('subscription_customer_add_post'))
            ->setFormMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_FRONTEND_FORM, $event);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_RETURN, $event);

        return $event->getResponse();
    }

    public function addPostAction(Request $request)
    {
        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setSection(CoreEvent::SECTION_FRONTEND)
            ->setEntity($this->get('cart.entity')->getInstance(CoreEntityConstants::CUSTOMER))
            ->setRequest($request)
            ->setUser($this->getUser())
            ->setFormAction($this->generateUrl('subscription_customer_add_post'))
            ->setFormMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_FRONTEND_FORM, $event);

        $form = $event->getReturnData('form');
        if ($form->handleRequest($request)->isValid()) {

            $formData = $request->request->get($form->getName());

            $event->setFormData($formData);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD, $event);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_POST_RETURN, $event);

            return $event->getResponse();
        }

        if ($request->get(\MobileCart\CoreBundle\Constants\ApiConstants::PARAM_RESPONSE_TYPE, '') == 'json') {

            $invalid = [];
            foreach($form->all() as $childKey => $child) {
                $errors = $child->getErrors();
                if ($errors->count()) {
                    $invalid[$childKey] = [];
                    foreach($errors as $error) {
                        $invalid[$childKey][] = $error->getMessage();
                    }
                }
            }

            return new JsonResponse([
                'success' => false,
                'invalid' => $invalid,
                'messages' => $event->getMessages()
            ]);
        }

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_RETURN, $event);

        return $event->getResponse();
    }

    public function addSuccessAction(Request $request)
    {
        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setSection(CoreEvent::SECTION_FRONTEND)
            ->setRequest($request)
            ->setUser($this->getUser());

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_SUCCESS, $event);

        return $event->getResponse();
    }

    public function removeAction(Request $request)
    {
        $customerId = $request->get('customer_id', 0);
        $user = $this->getUser();
        $parentSubscription = $this->get('cart.entity')->findOneBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
            'customer' => $user->getId(),
        ]);

        if (!$parentSubscription) {
            throw $this->createNotFoundException("Unable to find your subscription");
        }

        $entity = $this->get('cart.entity')->findOneBy($this->objectType, [
            'parent_subscription_customer' => $parentSubscription->getId(),
            'customer' => $customerId,
        ]);

        if (!$entity) {
            throw $this->createNotFoundException("Unable to find entity with Customer ID: {$customerId}");
        }

        $this->get('cart.entity')->remove($entity);

        if ($request->get(\MobileCart\CoreBundle\Constants\ApiConstants::PARAM_RESPONSE_TYPE, '') == 'json') {
            return new JsonResponse([
                'success' => true,
            ]);
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
