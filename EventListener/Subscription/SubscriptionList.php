<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;

class SubscriptionList
{

    protected $router;

    protected $themeService;

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

    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function onSubscriptionList(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $request = $event->getRequest();
        $format = $request->get(\MobileCart\CoreBundle\Constants\ApiConstants::PARAM_RESPONSE_TYPE, '');
        $response = '';

        $returnData['mass_actions'] =
        [
            [
                'label'         => 'Delete Subscriptions',
                'input_label'   => 'Confirm Mass-Delete ?',
                'input'         => 'mass_delete',
                'input_type'    => 'select',
                'input_options' => [
                    ['value' => 0, 'label' => 'No'],
                    ['value' => 1, 'label' => 'Yes'],
                ],
                'url' => $this->getRouter()->generate('cart_admin_subscription_mass_delete'),
                'external' => 0,
            ],
        ];

        $returnData['columns'] =
        [
            [
                'key' => 'id',
                'label' => 'ID',
                'sort' => 1,
            ],
            [
                'key' => 'name',
                'label' => 'Name',
                'sort' => 1,
            ],
            [
                'key' => 'payment_amount',
                'label' => 'Payment Amount',
                'sort' => 1,
            ],
        ];

        switch($format) {
            case 'json':
                $response = new JsonResponse($returnData);
                break;
            //case 'xml':
            //
            //    break;
            default:

                $response = $this->getThemeService()
                    ->render('subscription_admin', 'Subscription:index.html.twig', $returnData);

                break;
        }

        $event->setReturnData($returnData);
        $event->setResponse($response);
    }
}
