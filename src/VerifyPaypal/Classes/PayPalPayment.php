<?php

namespace VerifyPaypal\Classes;

Class PaypalPayment extends \VerifyPaypal\Classes\VerifyPaypal
{

	// seperate json groups
	private $client;
	private $payment;
	private $proof_of_payment;
	private $rest_api;
	private $adaptive_api;

	// across api variables
	private $platform;
	private $sdk;
	private $environment;
	private $product_name;

	private $description;
	private $amount;
	private $currency;

	// adaptive payments
	private $pay_key;
	private $app_id;

	// rest payments
	private $state;
	private $payment_id;

	protected $paymentType;

	public function __construct($viro)
	{
		parent::__construct($viro);
	}

	public function verify($json)
	{

		// parse json
		$json = json_decode($json, true);

		// seperate json groups
		$this->client = $json['client'];
		$this->payment = $json['payment'];
		$this->proof_of_payment = $json['proof_of_payment'];

		// across api variables
		$this->platform = $this->client['platform'];
		$this->sdk = $this->client['paypal_sdk_version'];
		$this->environment = $this->client['environment'];
		$this->product_name = $this->client['product_name'];

		$this->description = $this->payment['short_description'];
		$this->amount = $this->payment['amount'];
		$this->currency = $this->payment['currency_code'];

		// adaptive payments
		if (isset($this->proof_of_payment['adaptive_payment']))
		{
			$this->paymentType = "Paypal";

			$this->adaptive_api = $this->proof_of_payment['adaptive_payment'];
			$this->pay_key = $this->adaptive_api['pay_key'];
			$this->app_id = $this->adaptive_api['app_id'];

			$ap = new \VerifyPaypal\Classes\AdaptivePayment($this->viro);
			$ap->setKey($this->pay_key);
			$ap->setID($this->app_id);
			$ap->verify();

			// get status
			$this->status = $ap->getStatus();
		}

		// rest payments
		if (isset($this->proof_of_payment['rest_api']))
		{
			$this->paymentType = "Credit Card";

			$this->rest_api = $this->proof_of_payment['rest_api'];
			$this->payment_id = $this->rest_api['payment_id'];

			$rp = new \VerifyPaypal\Classes\RestPayment($this->viro);
			$rp->setPaymentID($this->payment_id);
			$rp->verify();

			// get status
			$this->status = $rp->getStatus();
		}
	}

	public function getPlatform()
	{
		return $this->platform;
	}

	public function getEnvironment()
	{
		return $this->environment;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	public function getCurrency()
	{
		return $this->currency;
	}

	public function getResult()
	{
		return $this->result;
	}

	public function getPaymentType()
	{
		return $this->paymentType;
	}
}