<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;

class SubscriptionCustomerFrontendList
{

    protected $router;

    protected $themeService;

    protected $entityService;

    protected $event;

    protected function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    protected function getEvent()
    {
        return $this->event;
    }

    protected function getReturnData()
    {
        return $this->getEvent()->getReturnData()
            ? $this->getEvent()->getReturnData()
            : [];
    }

    public function setThemeService($themeService)
    {
        $this->themeService = $themeService;
        return $this;
    }

    public function getThemeService()
    {
        return $this->themeService;
    }

    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    public function getEntityService()
    {
        return $this->entityService;
    }

    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function onSubscriptionCustomerFrontendList(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();
        $user = $returnData['user'];
        $request = $event->getRequest();
        $format = $request->get('format', '');
        $response = '';

        $entities = [];

        $subscriptionCustomer = $this->getEntityService()->findBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
            'customer' => $user->getId(),
        ]);

        if ($subscriptionCustomer) {

            $customer = $subscriptionCustomer->getCustomer();

            $entities[] = [
                'id' => $customer->getId(),
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'billing_phone' => $customer->getBillingPhone(),
            ];
        }

        $subscriptionCustomers = $this->getEntityService()->findBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
            'parent_subscription_customer' => $user->getId(),
        ]);

        if ($subscriptionCustomers) {


            foreach($subscriptionCustomers as $subscriptionCustomer) {
                $customer = $subscriptionCustomer->getCustomer();

                $entities[] = [
                    'id' => $customer->getId(),
                    'first_name' => $customer->getFirstName(),
                    'last_name' => $customer->getLastName(),
                    'name' => $customer->getName(),
                    'email' => $customer->getEmail(),
                    'billing_phone' => $customer->getBillingPhone(),
                ];
            }
        }

        $returnData['entities'] = $entities;

        switch($format) {
            case 'json':
                $response = new JsonResponse($returnData);
                break;
            //case 'xml':
            //
            //    break;
            default:

                $response = $this->getThemeService()
                    ->render('subscription_frontend', 'SubscriptionCustomer:list.html.twig', $returnData);

                break;
        }

        $event->setReturnData($returnData);
        $event->setResponse($response);
    }
}
