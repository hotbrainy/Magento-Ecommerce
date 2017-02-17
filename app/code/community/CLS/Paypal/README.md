## Saved Credit Cards via PayPal Tokenization

PCI Compliant saved credit cards with Paypal reference transactions

### PayPal Configuration

Please note that you must contact your PayPal representative to enable reference transaction support on your PayPal account in order to use this extension.

### Features

#### Saved Credit Card

* Customers can save their credit card information during the checkout process and then use the same credit card on future orders.    
* Our solution is fully PCI Compliant since it stores a secure token as a reference transaction to the credit card data. Credit card data is not actually stored on the web site.

#### Billing Agreements

* Billing agreements are now available with Payflow Pro integration.    
* Billing agreements can be created during Express Checkout even for guest or registering customers, for use with the new admin order management features.  
       
#### Admin Order Management

* When viewing an order, admins have the option to select "New Order from this Payment." This option allows the same PayPal billing agreement or credit card used on the previous order to be charged on the new order.  
* The ability to re-charge the previous PayPal billing agreement or credit card is also available when performing a re-order or editing an order.
* To better facilitate the use of the "New Order from this Payment" feature, the admin interface can now be used to create guest orders.

### What are PayPal Billing Agreements?

Billing Agreements allow you to bill customers at regular intervals.

Set up payments for dues, subscriptions, installments, and more.

* Automatically bill customers at specified intervals.
* Use automated notifications and reporting to manage billing status.
* Customers can complete transactions without leaving your app, game, or website.

### Supported PayPal Solutions

The features of this module are available for Magento integrations with the following PayPal solutions:

* Payments Pro
* Payments Advanced
* Payflow Pro
* Payflow Link
* Express Checkout (Billing Agreement features only)

### Configuration Instructions

Settings for this module's features can be found in System Configuration, under the various existing PayPal configuration groups.  In the Payment Methods tab, navigate to a specific PayPal solution (Payments Pro, for example) and open the the solution's Advanced Settings to find the new configuration areas.

PayPal Billing Agreement Settings can now be found under Advanced Settings for Payflow Pro, just as it is found for other solutions.

For each of the four supported direct payment solutions, Advanced Settings now contains three added groups:  PayPal Saved Credit Card Settings, PayPal Previous Order Credit Card Settings, and PayPal Previous Order Billing Agreement Settings. Enable the module's various features from these groups, as well as setting their configuration options independently of the standard credit card and billing agreement configurations.