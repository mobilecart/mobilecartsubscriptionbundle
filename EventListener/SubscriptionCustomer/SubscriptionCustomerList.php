<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerList
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerList
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \MobileCart\CoreBundle\Service\ThemeService
     */
    protected $themeService;

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

    public function onSubscriptionCustomerList(CoreEvent $event)
    {
        $request = $event->getRequest();
        $format = $request->get(\MobileCart\CoreBundle\Constants\ApiConstants::PARAM_RESPONSE_TYPE, '');

        $event->setReturnData('mass_actions', [
            [
                'label'         => 'Delete Subscription Customers',
                'input_label'   => 'Confirm Mass-Delete ?',
                'input'         => 'mass_delete',
                'input_type'    => 'select',
                'input_options' => [
                    ['value' => 0, 'label' => 'No'],
                    ['value' => 1, 'label' => 'Yes'],
                ],
                'url' => $this->getRouter()->generate('cart_admin_subscription_customer_mass_delete'),
                'external' => 0,
            ],
        ]);

        $event->setReturnData('columns', [
            [
                'key' => 'id',
                'label' => 'ID',
                'sort' => true,
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'sort' => true,
            ],
            [
                'key' => 'customer_name',
                'label' => 'Customer Name',
                'sort' => true,
            ],
            [
                'key' => 'parent_subscription_customer_id',
                'label' => 'Parent Subscription',
                'sort' => true,
            ],
            [
                'key' => 'created_at',
                'label' => 'Created At',
                'sort' => true,
            ],
            [
                'key' => 'service_account_id',
                'label' => 'Account',
                'sort' => true,
            ],
        ]);

        switch($format) {
            case 'json':
                $event->setResponse(new JsonResponse($event->getReturnData()));
                break;
            default:
                $event->setResponse($this->getThemeService()->render(
                    'subscription_admin',
                    'SubscriptionCustomer:index.html.twig',
                    $event->getReturnData()
                ));
                break;
        }
    }
}
