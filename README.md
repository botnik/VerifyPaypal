# VerifyPaypal

_VerifyPaypal is a PHP class to verify Paypal SDK payments, it verifies both credit card and paypal account transactions using the REST API and Adaptive Payments._

## Known Issues

Paypal is currently having an issue with the Rest API in sandbox mode. Testing a credit card will result in an Invalid Resourse ID error. Hopefully this gets fixed soon.

## Getting Started

- Start by cloning VerifyPaypal into your project:

```
git clone https://github.com/redfro/VerifyPaypal.git
```

- Next, provided you have [composer](http://getcomposer.org) installed, run the following command:

```bash
$ php composer.phar install
```

- This will install the library into a vendor folder. Now add the autoloader to your php files where applicable.

```php
require 'VerifyPaypal/vendor/autoload.php';
```

- You need to `use` the PaypalPayment class, so add this directly under the autoloader.

```php
use VerifyPaypal\Classes\PaypalPayment;
```

- Now update VerifyPaypalConfig.php `(src/VerifyPaypal/Config/VerifyPaypalConfig.php)` with your paypal information:

```php
/*==========  Live Credentials  ==========*/

define('CLIENT_ID', '');
define('SECRET_KEY', '');
define('PAYPAL_ID', '');
define('PAYPAL_PW', '');
define('PAYPAL_SIG', '');
define('RECEIVER_EMAIL', '');

/*==========  Sandbox Credentials  ==========*/

define('CLIENT_ID_SANDBOX', '');
define('SECRET_KEY_SANDBOX', '');
define('PAYPAL_ID_SANDBOX', '');
define('PAYPAL_PW_SANDBOX', '');
define('PAYPAL_SIG_SANDBOX', '');
define('RECEIVER_EMAIL_SANDBOX', '');
``` 

- Create a new PaypalPayment() and set the environment to either `"sandbox"` or `"live"`:

```php
$payment = new PaypalPayment("sandbox");
```

- Pass the JSON sent from the Paypal SDK into verify():

```php
$payment->verify($json);
```

The verify() method returns true if the payment is valid, false if not.

- You can check the status returned from Paypal using:

```php
$payment->getStatus();
```

## Example

```php
require 'vendor/autoload.php';
use VerifyPaypal\Classes\PaypalPayment;

$payment = new PaypalPayment("sandbox");
$validPayment = $payment->verify($json);

if ($validPayment)
{
	// do something with valid payment
}

echo $payment->getStatus();  // check status message from paypal
```

## Testing

You can test if VerifyPaypal is installed correctly by running the following command.

```bash
$ php tests/test.php
```

This should return "VerifyPaypal was installed correctly!". 

## Contributing

I have no idea what I am doing and so please feel free to contribute, I am always looking to learn.

## License

VerifyPaypal is licensed under the MIT license. See the LICENSE file for more details.