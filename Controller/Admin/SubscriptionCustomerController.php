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

use MobileCart\SubscriptionBundle\Constants\EntityConstants;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Event\SubscriptionEvents;

/**
 * Subscription customer controller.
 *
 * @Route("/admin/subscription_customer")
 */
class SubscriptionCustomerController extends Controller
{

    protected $objectType = EntityConstants::SUBSCRIPTION_CUSTOMER;

    /**
     * Lists Subscription entities.
     *
     * @Route("/", name="cart_admin_subscription_customer")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        // Load a service; which extends Search\SearchAbstract
        // The service parameter is stored in the service configuration as a parameter ; (slightly meta)
        // This service could use either MySQL or ElasticSearch, etc for retrieving item data
        $searchParam = $this->container->getParameter('cart.load.admin');
        $search = $this->container->get($searchParam);

        // Observe Event :
        //  perform custom logic, post-processing

        $event = new CoreEvent();
        $event->setRequest($request)
            ->setSearch($search)
            ->setObjectType($this->objectType)
            ->setSection(CoreEvent::SECTION_BACKEND);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_SEARCH, $event);

        $search = $event->getSearch();

        if (in_array($search->getFormat(), ['', 'html'])) {
            // for storing the last grid filters in the url ; used in back links
            $request->getSession()->set('cart_admin_subscription_customer', $request->getQueryString());
        }

        // Data for Template, etc
        $returnData = [
            'search' => $search,
            'result' => $search->getResult(),
        ];

        // Observe Event :
        //  populate grid columns and mass actions,
        //  continue building return data

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setRequest($request)
            ->setReturnData($returnData);

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_LIST, $event);

        return $event->getResponse();
    }

    /**
     * Creates a new Subscription entity.
     *
     * @Route("/", name="cart_admin_subscription_customer_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = $this->get('cart.entity')->getInstance(EntityConstants::SUBSCRIPTION_CUSTOMER);

        $formEvent = new CoreEvent();
        $formEvent->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setAction($this->generateUrl('cart_admin_subscription_customer_create'))
            ->setMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $formEvent);

        $form = $formEvent->getForm();

        if ($form->handleRequest($request)->isValid()) {

            $formData = $request->request->get($form->getName());

            // observe event
            //  add subscription to indexes, etc
            $event = new CoreEvent();
            $event->setEntity($entity)
                ->setRequest($request)
                ->setFormData($formData);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_INSERT, $event);

            $returnEvent = new CoreEvent();
            $returnEvent->setMessages($event->getMessages());
            $returnEvent->setRequest($request);
            $returnEvent->setEntity($entity);
            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_CREATE_RETURN, $returnEvent);

            return $returnEvent->getResponse();
        }

        if ($request->get('format', '') == 'json') {

            $invalid = [];
            $messages = [];
            foreach($form->all() as $childKey => $child) {
                $errors = $child->getErrors();
                if ($errors->count()) {
                    $invalid[$childKey] = [];
                    foreach($errors as $error) {
                        $invalid[$childKey][] = $error->getMessage();
                    }
                }
            }

            $returnData = [
                'success' => 0,
                'invalid' => $invalid,
                'messages' => $messages,
            ];

            return new JsonResponse($returnData);
        }

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setRequest($request)
            ->setEntity($entity)
            ->setReturnData($formEvent->getReturnData());

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_NEW_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Displays a form to create a new Subscription entity.
     *
     * @Route("/new", name="cart_admin_subscription_customer_new")
     * @Method("GET")
     */
    public function newAction(Request $request)
    {
        $entity = $this->get('cart.entity')->getInstance($this->objectType);

        $formEvent = new CoreEvent();
        $formEvent->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setAction($this->generateUrl('cart_admin_subscription_customer_create'))
            ->setMethod('POST');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $formEvent);

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setReturnData($formEvent->getReturnData());

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_NEW_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Finds and displays a Subscription entity.
     *
     * @Route("/{id}", name="cart_admin_subscription_customer_show")
     * @Method("GET")
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
     * Displays a form to edit an existing Subscription entity.
     *
     * @Route("/{id}/edit", name="cart_admin_subscription_customer_edit")
     * @Method("GET")
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->get('cart.entity')->find($this->objectType, $id);

        if (!$entity) {
            throw $this->createNotFoundException("Unable to find entity with ID: {$id}");
        }

        $formEvent = new CoreEvent();
        $formEvent->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setAction($this->generateUrl('cart_admin_subscription_customer_update', ['id' => $entity->getId()]))
            ->setMethod('PUT');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $formEvent);

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setReturnData($formEvent->getReturnData());

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_EDIT_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Edits an existing Subscription entity.
     *
     * @Route("/{id}", name="cart_admin_subscription_customer_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->get('cart.entity')->find($this->objectType, $id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subscription entity.');
        }

        $formEvent = new CoreEvent();
        $formEvent->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setAction($this->generateUrl('cart_admin_subscription_customer_update', ['id' => $entity->getId()]))
            ->setMethod('PUT');

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_ADMIN_FORM, $formEvent);

        $form = $formEvent->getForm();

        if ($form->handleRequest($request)->isValid()) {

            $formData = $request->request->get($form->getName());

            // observe event
            // update entity via command bus
            $event = new CoreEvent();
            $event->setObjectType($this->objectType)
                ->setEntity($entity)
                ->setRequest($request)
                ->setFormData($formData);

            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_UPDATE, $event);

            $returnEvent = new CoreEvent();
            $returnEvent->setMessages($event->getMessages());
            $returnEvent->setRequest($request);
            $returnEvent->setEntity($entity);
            $this->get('event_dispatcher')
                ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_UPDATE_RETURN, $returnEvent);

            return $returnEvent->getResponse();
        }

        if ($request->get('format', '') == 'json') {

            $invalid = [];
            $messages = [];
            foreach($form->all() as $childKey => $child) {
                $errors = $child->getErrors();
                if ($errors->count()) {
                    $invalid[$childKey] = [];
                    foreach($errors as $error) {
                        $invalid[$childKey][] = $error->getMessage();
                    }
                }
            }

            $returnData = [
                'success' => 0,
                'invalid' => $invalid,
                'messages' => $messages,
            ];

            return new JsonResponse($returnData);
        }

        $event = new CoreEvent();
        $event->setObjectType($this->objectType)
            ->setEntity($entity)
            ->setRequest($request)
            ->setReturnData($formEvent->getReturnData());

        $this->get('event_dispatcher')
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_CUSTOMER_EDIT_RETURN, $event);

        return $event->getResponse();
    }

    /**
     * Cancels an existing Subscription entity.
     *
     * @Route("/{id}/cancel", name="cart_admin_subscription_customer_cancel")
     * @Method("PUT")
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
     * Deletes a Subscription entity.
     *
     * @Route("/{id}", name="cart_admin_subscription_customer_delete")
     * @Method("DELETE")
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
     *
     * @Route("/mass_delete", name="cart_admin_subscription_customer_mass_delete")
     * @Method("POST")
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
