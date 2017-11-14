# mobilecartsubscriptionbundle
Subscription Management for Mobile Cart

Install into your app/AppKernel.php . place it after MobileCartCoreBundle()

Update the Database, add new tables:

$ ./app/console doctrine:schema:update

Add a Variant with the code subscription_id to the Variant Set for Products.
There is a CLI command also:

$ ./app/console cart:sub:itemvars

