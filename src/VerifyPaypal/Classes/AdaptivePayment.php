<?php

namespace VerifyPaypal\Classes;

Class AdaptivePayment extends \VerifyPaypal\Classes\VerifyPaypal
{
	private $payKey;
	private $appID;

	public function __construct($viro)
	{
		parent::__construct($viro);
	}

	public function setKey($key)
	{
		$this->payKey = $key;
	}

	private function getKey()
	{
		if (!$this->payKey)
			throw new \Exception('Pay key not set.');

		return $this->payKey;
	}

	public function setID($id)
	{
		$this->appID = $id;
	}

	private function getID()
	{
		if (!$this->appID)
			throw new \Exception('App ID not set.');

		return $this->appID;
	}

	public function verify()
	{

		$environment = "";
		$result = true;
		$status = "approved";

		if ($this->viro == "sandbox")
			$environment = "sandbox.";

		$key = "payKey=" . $this->getKey() . "&requestEnvelope.errorLanguage=en_US";
		$url = "https://svcs." . $environment . "paypal.com/AdaptivePayments/PaymentDetails";
		$header = array(
		    "X-PAYPAL-SECURITY-USERID: " . $this->paypalID,
			"X-PAYPAL-SECURITY-PASSWORD: " . $this->paypalPW,
			"X-PAYPAL-SECURITY-SIGNATURE: " . $this->paypalSIG,
			"X-PAYPAL-REQUEST-DATA-FORMAT: NV",
			"X-PAYPAL-RESPONSE-DATA-FORMAT: NV",
			"X-PAYPAL-APPLICATION-ID: " . $this->getID(),
			"Content-length: " . strlen($key)
		);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $key); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		// received from paypal
		$response = curl_exec($ch);
		curl_close($ch);

		// json_decode() not working with returned json
		parse_str($response, $return_from_paypal);

		// check for status complete
		$return_status = $return_from_paypal['status'];
		$return_sender_transaction_status = $return_from_paypal['paymentInfoList_paymentInfo(0)_senderTransactionStatus'];

		// check for matching
		$return_currency = $return_from_paypal['currencyCode'];
		$return_receiver_email = $return_from_paypal['paymentInfoList_paymentInfo(0)_receiver_email'];
		$return_amount = $return_from_paypal['paymentInfoList_paymentInfo(0)_receiver_amount'];

		if ($return_status !== "COMPLETED")
		{
			$status = $return_status;
			$result = false;
		}
		else if ($return_sender_transaction_status !== "COMPLETE")
		{
			$status = $return_sender_transaction_status;
			$result = false;
		}
		else if (($return_currency !== $currency) || ($return_amount !== $amount) || ($return_receiver_email !== $receiver_email))
		{
			$status = "Payment information does not match";
			$result = false;
		}

		$this->status = $status;

		return $result;
	}
}

?>