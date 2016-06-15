<?php

namespace MobileCart\SubscriptionBundle\Event;

class SubscriptionEvents
{
    const SUBSCRIPTION_LIST = 'subscription.list';
    const SUBSCRIPTION_SEARCH = 'subscription.search';
    const SUBSCRIPTION_UPDATE = 'subscription.update';
    const SUBSCRIPTION_INSERT = 'subscription.insert';
    const SUBSCRIPTION_DELETE = 'subscription.delete';
    const SUBSCRIPTION_SAVE = 'subscription.save';
    const SUBSCRIPTION_EDIT_RETURN = 'subscription.edit.return';
    const SUBSCRIPTION_NEW_RETURN = 'subscription.new.return';
    const SUBSCRIPTION_VIEW_RETURN = 'subscription.view.return';
    const SUBSCRIPTION_CREATE_RETURN = 'subscription.create.return';
    const SUBSCRIPTION_UPDATE_RETURN = 'subscription.update.return';
    const SUBSCRIPTION_ADMIN_FORM = 'subscription.admin.form';

    const SUBSCRIPTION_CUSTOMER_LIST = 'subscription_customer.list';
    const SUBSCRIPTION_CUSTOMER_FRONTEND_LIST = 'subscription_customer.frontend.list';
    const SUBSCRIPTION_CUSTOMER_SEARCH = 'subscription_customer.search';
    const SUBSCRIPTION_CUSTOMER_UPDATE = 'subscription_customer.update';
    const SUBSCRIPTION_CUSTOMER_INSERT = 'subscription_customer.insert';
    const SUBSCRIPTION_CUSTOMER_DELETE = 'subscription_customer.delete';
    const SUBSCRIPTION_CUSTOMER_SAVE = 'subscription_customer.save';
    const SUBSCRIPTION_CUSTOMER_EDIT_RETURN = 'subscription_customer.edit.return';
    const SUBSCRIPTION_CUSTOMER_NEW_RETURN = 'subscription_customer.new.return';
    const SUBSCRIPTION_CUSTOMER_ADD = 'subscription_customer.add';
    const SUBSCRIPTION_CUSTOMER_ADD_SUCCESS = 'subscription_customer.add.success';
    const SUBSCRIPTION_CUSTOMER_ADD_RETURN = 'subscription_customer.add.return';
    const SUBSCRIPTION_CUSTOMER_ADD_POST_RETURN = 'subscription_customer.add.post.return';
    const SUBSCRIPTION_CUSTOMER_VIEW_RETURN = 'subscription_customer.view.return';
    const SUBSCRIPTION_CUSTOMER_CREATE_RETURN = 'subscription_customer.create.return';
    const SUBSCRIPTION_CUSTOMER_UPDATE_RETURN = 'subscription_customer.update.return';
    const SUBSCRIPTION_CUSTOMER_ADMIN_FORM = 'subscription_customer.admin.form';
    const SUBSCRIPTION_CUSTOMER_FRONTEND_FORM = 'subscription_customer.frontend.form';
}
