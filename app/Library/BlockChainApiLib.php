<?php

namespace App\Library;

use App\Models\UserPlan;

class BlockChainApiLib 
{
	protected $callback_url;
	protected $xpub_key;
	protected $secret_key;
	protected $api_key;
	protected $api_gen_addr_url;
	protected $to_btc_url;

	public function __construct()
	{
		$this->xpub_key 		= env('BLOCKCHAIN_XPUB_KEY');
		$this->api_key 			= env('BLOCKCHAIN_API_KEY');
		$this->secret_key 		= env('BLOCKCHAIN_SECRET_KEY');
		$this->callback_url 	= route('blockchain.callback');
		$this->api_gen_addr_url = env('BLOCKCHAIN_GEN_ADDR_URL');
		$this->to_btc_url 		= env('BLOCKCHAIN_TO_BTC_URL');
	}

	public function get_callback_url()
	{
		return $this->callback_url;
	}

	public function get_api_key()
	{
		return $this->api_key;
	}

	public function get_secret_key()
	{
		return $this->secret_key;
	}

	public function generate_wallet_address($transaction_no)
	{
		$callback_url_params = [
			'transaction_no' 	=> $transaction_no,
			'secret'			=> $this->secret_key
		];

		$callback_url = $this->callback_url . '?' . http_build_query($callback_url_params);

		$url_params = [
			'xpub' 		=> $this->xpub_key,
			'callback'	=> $callback_url,
			'key'		=> $this->api_key,
			'gap_limit'	=> 5000
		];

		$url = $this->api_gen_addr_url . '?' . http_build_query($url_params);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

		$output = curl_exec($ch); 

		curl_close($ch); 

		$decode_output = json_decode($output, true);

		if (array_key_exists('address', $decode_output))
		{
			return $decode_output['address'];
		}

		return false;
	}

	public function get_to_btc_value()
	{
		$plan = UserPlan::where('name', 'level-1')->first();

        $url_params = [
        	'currency'	=> 'USD',
        	'value'		=> $plan->price
        ];

        $url = $this->to_btc_url . '?' . http_build_query($url_params);


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
	}

	public function get_usd_to_btc_value($value)
	{
		$url_params = [
        	'currency'	=> 'USD',
        	'value'		=> $value
        ];

		$url = $this->to_btc_url . '?' . http_build_query($url_params);


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
	}
}