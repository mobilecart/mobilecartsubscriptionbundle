<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerAddPostReturn
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerAddPostReturn
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

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

    public function onSubscriptionCustomerAddPostReturn(CoreEvent $event)
    {
        $success = (bool) $event->getReturnData('success', false);
        $request = $event->getRequest();
        $format = $request->get(\MobileCart\CoreBundle\Constants\ApiConstants::PARAM_RESPONSE_TYPE, '');

        $params = [];
        $route = 'subscription_customer_add_success';
        $url = $this->getRouter()->generate($route, $params);

        if ($event->getRequest()->getSession() && $event->getMessages()) {
            foreach($event->getMessages() as $code => $messages) {
                if (!$messages) {
                    continue;
                }
                foreach($messages as $message) {
                    $event->getRequest()->getSession()->getFlashBag()->add($code, $message);
                }
            }
        }

        switch($format) {
            case 'json':
                $event->setResponse(new JsonResponse([
                    'success' => $success,
                ]));
                break;
            default:
                $event->setResponse(new RedirectResponse($url));
                break;
        }
    }

}
