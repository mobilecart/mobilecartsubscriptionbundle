<?php

/*
 * This file is part of the Mobile Cart package.
 *
 * (c) Jesse Hanson <jesse@mobilecart.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MobileCart\SubscriptionBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Event\SubscriptionEvents;

/**
 * Subscription customer controller
 */
class SubscriptionCustomerController extends Controller
{
    /**
     * @var string
     */
    protected $objectType = EntityConstants::SUBSCRIPTION_CUSTOMER;

    /**
     * Lists Subscription entities
     */
    public function indexAction(Request $request)
    {
        $event = new CoreEvent();
        $event->setRequest($request)
            ->setObjectType($this->objectType)
            ->setSection(CoreEvent::SECTION_BACKEND);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_SEARCH, $event);

        return $event->getResponse();
    }

    /**
     * Creates a new Subscription entity
     */
    public function createAction(Request $request)
    {
        $entity = $this->get('cart.entity')->getInstance(EntityConstants::SUBSCRIPTION_CUSTOMER);

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setFormAction($this->generateUrl('cart_admin_subscription_customer_create'))
            ->setFormMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $event);

        $form = $event->getReturnData('form');
        if ($form->handleRequest($request)->isValid()) {

            $formData = $request->request->get($form->getName());

            $event->setFormData($formData);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_INSERT, $event);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_CREATE_RETURN, $event);

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
                'messages' => $event->getMessages(),
            ]);
        }

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_NEW_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Displays a form to create a new Subscription entity
     */
    public function newAction(Request $request)
    {
        $entity = $this->get('cart.entity')->getInstance($this->objectType);

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setFormAction($this->generateUrl('cart_admin_subscription_customer_create'))
            ->setFormMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $event);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_NEW_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Finds and displays a Subscription entity
     */
    public function showAction(Request $request, $id)
    {
        $entity = $this->get('cart.entity')->find($this->objectType, $id);
        if (!$entity) {
            throw $this->createNotFoundException("Unable to find entity with ID: {$id}");
        }

        return new JsonResponse($entity->getData());
    }

    /**
     * Displays a form to edit an existing Subscription entity
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->get('cart.entity')->find($this->objectType, $id);
        if (!$entity) {
            throw $this->createNotFoundException("Unable to find entity with ID: {$id}");
        }

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setFormAction($this->generateUrl('cart_admin_subscription_customer_update', ['id' => $entity->getId()]))
            ->setFormMethod('PUT');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $event);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_EDIT_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Edits an existing Subscription entity
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->get('cart.entity')->find($this->objectType, $id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subscription entity.');
        }

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setFormAction($this->generateUrl('cart_admin_subscription_customer_update', ['id' => $entity->getId()]))
            ->setFormMethod('PUT');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $event);

        $form = $event->getReturnData('form');
        if ($form->handleRequest($request)->isValid()) {

            $formData = $request->request->get($form->getName());

            $event->setFormData($formData);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_UPDATE, $event);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_UPDATE_RETURN, $event);

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
                'messages' => $event->getMessages(),
            ]);
        }

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_EDIT_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Cancels an existing Subscription entity
     */
    public function cancelAction(Request $request, $id)
    {
        $entity = $this->get('cart.entity')->find($this->objectType, $id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subscription entity.');
        }

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_CANCEL, $event);

        return $event->getResponse()
            ? $event->getResponse()
            : $this->redirect($this->generateUrl('cart_admin_subscription_customer'));
    }

    /**
     * Deletes a Subscription entity
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->get('cart.entity')->find($this->objectType, $id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subscription entity.');
        }

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_DELETE, $event);

        return $event->getResponse()
            ? $event->getResponse()
            : $this->redirect($this->generateUrl('cart_admin_subscription_customer'));
    }

    /**
     * Mass-Delete Subscriptions
     */
    public function massDeleteAction(Request $request)
    {
        $itemIds = $request->get('item_ids', []);
        $returnData = ['item_ids' => []];

        if ($itemIds) {
            foreach($itemIds as $itemId) {
                $entity = $this->get('cart.entity')->find($this->objectType, $itemId);
                if (!$entity) {
                    $returnData['error'][] = $itemId;
                    continue;
                }

                $event = new CoreEvent();
                $event->setObjectType($this->objectType)
                    ->setEntity($entity)
                    ->setRequest($request);

                $this->get('event_dispatcher')
                    ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_DELETE, $event);

                $returnData['item_ids'][] = $itemId;
            }

            $request->getSession()->getFlashBag()->add(
                'success',
                count($returnData['item_ids']) . ' Subscriptions Successfully Deleted'
            );
        }

        return new JsonResponse($returnData);
    }

    /**
     * Creates a form to delete an entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cart_admin_subscription_customer_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', ['label' => 'Delete'])
            ->getForm();
    }
}
