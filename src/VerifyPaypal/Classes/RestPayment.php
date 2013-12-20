<?php

namespace VerifyPaypal\Classes;

Class RestPayment extends \VerifyPaypal\Classes\VerifyPaypal
{
	private $paymentID;

	public function __construct($viro)
	{
		parent::__construct($viro);
	}

	public function setPaymentID($id)
	{
		$this->paymentID = $id;
	}

	public function verify()
	{
		$environment = "";
		$status = "approved";
		$result = true;

		if ($this->viro == "sandbox")
			$environment = "sandbox.";

		$url = "https://api." . $environment . "paypal.com/v1/payments/payment/". $this->paymentID;
		$header = array(
			"Content-Type:application/json",
			"Authorization:Bearer " . $this->getAccessToken()
			);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		$response = curl_exec($ch);

		$result_from_paypal = json_decode($response, true);

		$return_transaction = $return_from_paypal['transactions'][0];

		// check state for approved
		$return_state_approved = $return_from_paypal['state'];

		// check total and currency for match
		$return_amount = $return_transaction['amount'];
		$return_total = $return_amount['total'];
		$return_currency = $return_amount['currency'];

		// check state for complete
		$return_related_resources = $return_transaction['related_resources'][0]['sale'];
		$return_state_complete = $return_related_resources['state'];

		if ($return_state_approved !== "approved" || $return_total !== $amount || $return_currency !== $currency || $return_state_complete !== 'completed')
			$result = 'Payment information does not match';

		curl_close($ch);

		$this->status = $status;

		return $result;
	}

	private function getAccessToken()
	{
		$environment = "";

		if ($this->viro == "sandbox")
			$environment = "sandbox.";

		$url = "https://api." . $environment . "paypal.com/v1/oauth2/token";

		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_USERPWD, $this->clientID.":".$this->secretKey);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

		$result_for_bearer = curl_exec($ch);
		$json_bearer = json_decode($result_for_bearer, true);

		curl_close($ch);

		return $json_bearer['access_token'];
	}
}

?>