<?php

namespace VerifyPaypal\Classes;

require_once __DIR__ . '/../Config/VerifyPaypalConfig.php';

Class VerifyPaypal
{
	protected $clientID;
	protected $secretKey;
	protected $paypalID;
	protected $paypalPW;
	protected $paypalSIG;
	protected $receiverEmail;
	protected $viro;
	protected $status;

	public function __construct($viro)
	{
		if ($viro == "live")
		{
			$this->clientID = CLIENT_ID;
			$this->secretKey = SECRET_KEY;
			$this->paypalID = PAYPAL_ID;
			$this->paypalPW = PAYPAL_PW;
			$this->paypalSIG = PAYPAL_SIG;
			$this->receiverEmail = RECEIVER_EMAIL;
		}
		else if ($viro == "sandbox")
		{
			$this->clientID = CLIENT_ID_SANDBOX;
			$this->secretKey = SECRET_KEY_SANDBOX;
			$this->paypalID = PAYPAL_ID_SANDBOX;
			$this->paypalPW = PAYPAL_PW_SANDBOX;
			$this->paypalSIG = PAYPAL_SIG_SANDBOX;
			$this->receiverEmail = RECEIVER_EMAIL_SANDBOX;
		}
		else
			throw new \Exception('Invalid enviornment, "live" or "sandbox" only.');

		$this->viro = $viro;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public static function isWorking()
	{
		echo "VerifyPaypal was installed correctly!";
	}
}

?>