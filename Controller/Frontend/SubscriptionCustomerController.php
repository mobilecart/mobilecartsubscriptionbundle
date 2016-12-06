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

use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Constants\EntityConstants as CoreEntityConstants;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Event\SubscriptionEvents;

class SubscriptionCustomerController extends Controller
{

    protected $objectType = EntityConstants::SUBSCRIPTION_CUSTOMER;

    /**
     * @Route("/customer/subscription/shared", name="subscription_customers")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $returnData = [
            'user' => $user,
        ];

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setRequest($request)
            ->setReturnData($returnData)
            ->setSection(CoreEvent::SECTION_FRONTEND);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_FRONTEND_LIST, $event);

        return $event->getResponse();
    }

    /**
     * @Route("/customer/subscription/shared/add", name="subscription_customer_add")
     * @Method("GET")
     */
    public function addAction(Request $request)
    {
        $user = $this->getUser();

        $returnData = [
            'user' => $user,
        ];

        $entity = $this->get('cart.entity')->getInstance(CoreEntityConstants::CUSTOMER);
        $formEvent = new CoreEvent();
        $formEvent->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setReturnData($returnData)
            ->setAction($this->generateUrl('subscription_customer_add_post'))
            ->setMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_FRONTEND_FORM, $formEvent);

        $form = $formEvent->getForm();
        $returnData = $formEvent->getReturnData();
        $returnData['form'] = $form;

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setRequest($request)
            ->setReturnData($returnData)
            ->setSection(CoreEvent::SECTION_FRONTEND);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * @Route("/customer/subscription/shared/add", name="subscription_customer_add_post")
     * @Method("POST")
     */
    public function addPostAction(Request $request)
    {
        $user = $this->getUser();

        $returnData = [
            'user' => $user,
        ];

        $entity = $this->get('cart.entity')->getInstance(CoreEntityConstants::CUSTOMER);
        $formEvent = new CoreEvent();
        $formEvent->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setReturnData($returnData)
            ->setAction($this->generateUrl('subscription_customer_add_post'))
            ->setMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_FRONTEND_FORM, $formEvent);

        $form = $formEvent->getForm();

        if ($form->handleRequest($request)->isValid()) {

            $formData = $request->request->get($form->getName());

            // observe event
            //  add subscription to indexes, etc
            $event = new CoreEvent();
            $event->setEntity($entity)
                ->setRequest($request)
                ->setReturnData($formEvent->getReturnData())
                ->setFormData($formData);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD, $event);

            $returnEvent = new CoreEvent();
            $returnEvent->setMessages($event->getMessages())
                ->setRequest($request)
                ->setReturnData($event->getReturnData())
                ->setEntity($entity);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_POST_RETURN, $returnEvent);

            return $returnEvent->getResponse();
        }

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setRequest($request)
            ->setReturnData($returnData)
            ->setSection(CoreEvent::SECTION_FRONTEND);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * @Route("/customer/subscription/shared/add/success", name="subscription_customer_add_success")
     * @Method("GET")
     */
    public function addSuccessAction(Request $request)
    {
        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setRequest($request);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADD_SUCCESS, $event);

        return $event->getResponse();
    }

    /**
     * @Route("/customer/subscription/shared/remove", name="subscription_customer_remove")
     * @Method("POST")
     */
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
                'success' => 1,
            ]);
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
