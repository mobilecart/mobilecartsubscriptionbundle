<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\HttpFoundation\JsonResponse;
use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

/**
 * Class SubscriptionCustomerFrontendList
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerFrontendList
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    protected $entityService;

    /**
     * @var \MobileCart\CoreBundle\Service\ThemeService
     */
    protected $themeService;

    /**
     * @param \MobileCart\CoreBundle\Service\AbstractEntityService $entityService
     * @return $this
     */
    public function setEntityService(\MobileCart\CoreBundle\Service\AbstractEntityService $entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

    /**
     * @param \MobileCart\CoreBundle\Service\ThemeService $themeService
     * @return $this
     */
    public function setThemeService(\MobileCart\CoreBundle\Service\ThemeService $themeService)
    {
        $this->themeService = $themeService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\ThemeService
     */
    public function getThemeService()
    {
        return $this->themeService;
    }

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @return $this
     */
    public function setRouter(\Symfony\Component\Routing\RouterInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    public function onSubscriptionCustomerFrontendList(CoreEvent $event)
    {
        $user = $event->getReturnData('user');
        $request = $event->getRequest();
        $format = $request->get(\MobileCart\CoreBundle\Constants\ApiConstants::PARAM_RESPONSE_TYPE, '');

        $entities = [];

        $subscriptionCustomer = $this->getEntityService()->findOneBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
            'customer' => $user->getId(),
        ]);

        if ($subscriptionCustomer) {

            //$customer = $subscriptionCustomer->getCustomer();

            /*
            $entities[] = [
                'id' => $customer->getId(),
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'billing_phone' => $customer->getBillingPhone(),
            ]; //*/

            $subscriptionCustomers = $this->getEntityService()->findBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
                'parent_subscription_customer' => $subscriptionCustomer->getId(),
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
        }

        $event->setReturnData('entities', $entities);

        switch($format) {
            case 'json':
                $event->setResponse(new JsonResponse($event->getReturnData()));
                break;
            default:
                $event->setResponse($this->getThemeService()->render(
                    'subscription_frontend',
                    'SubscriptionCustomer:list.html.twig',
                    $event->getReturnData()
                ));
                break;
        }
    }
}
