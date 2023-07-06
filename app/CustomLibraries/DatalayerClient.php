<?php

namespace App\CustomLibraries;

use Illuminate\Support\Facades\Config;

/**
 * DatalayerClient
 * 
 * @brief Waybeo Datalayer Client Library
 * @description Waybeo Datalayer Client Library
 * @todo update the credentials in .env file or to a constant 
 * @author Jaison John <jaison.john@waybeo.com>
 * @date 12 April 2020
 * @version  v1.0
 * 
 */
class DatalayerClient
{
	private $apiUrl = null;
	private $apiAccessToken = null;
	private $apiUser = null;
	private $apiPassword = null;

	
	function __construct()
	{
		$datalayer = Config::get('database.connections.datalayer');

		if(empty($datalayer['api_url'])) die("Unable to connect to Datalayer missing API_URL");
		$this->apiUrl = $datalayer['api_url'];

		if(empty($datalayer['api_access_token'])) die("Unable to connect to Datalayer missing API_ACCESS_TOKEN");
		$this->apiAccessToken = $datalayer['api_access_token'];

		if(empty($datalayer['api_user'])) die("Unable to connect to Datalayer missing API_USER");
		$this->apiUser = $datalayer['api_user'];

		if(empty($datalayer['api_password'])) die("Unable to connect to Datalayer missing API_PASSWORD");
		$this->apiPassword = $datalayer['api_password'];

	}

	public function updateEnvFile($value, $key = 'API_ACCESS_TOKEN')
    {
        $path = app()->environmentFilePath();

        $escaped = preg_quote('='.env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }

	public function getAccessToken()
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/user/login",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];

		$data = [
			"email" => $this->apiUser,
			"password" => $this->apiPassword
		];

		$curl_response = $this->requestApi($conf, $data);

		if($curl_response['response_code'] == 200) {

			$parsed_response = json_decode($curl_response['response'], true);
			$auth_token = $parsed_response["auth-token"];

			// $this->updateEnvFile($auth_token);

			return $auth_token;

		} else {

			die(print_r($curl_response));
		}

		return;
	}

	public function createCallLog($data)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/call",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];

		return $this->requestApi($conf, $data);
	}

	public function getCallLog($id)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/call/{$id}",
			'requestType' => 'GET',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$postData = [];
		return $this->requestApi($conf, $postData);
	}

	public function createManyCallLogs($data)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/call/many",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$postData = $data;

		return $this->requestApi($conf, $postData);
	}

	public function updateCallLog($id, $data)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/call/{$id}",
			'requestType' => 'PUT',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$postData = $data;
		
		return $this->requestApi($conf, $postData);
	}

	public function deleteCallLog($id)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/call/{$id}",
			'requestType' => 'DELETE',
			'headers' => [
				"auth-token: {$this->apiAccessToken}"
			]

		];
		$postData = [];

		return $this->requestApi($conf, $postData);
	}

	public function getPaginatedCallLogs($page, $recordsPerPage, $conditions)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/calllogs/paginate",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$data = [
			"page_number" => $page,
			"results_per_page" => $recordsPerPage,
			"conditions" => $conditions
		];
		return $this->requestApi($conf, $data);
		
	}

	public function getCount($conditions)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/calllogs/count",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$data = [
			"conditions" => $conditions
		];

		return $this->requestApi($conf, $data);
		
	}

	public function export($conditions, $fields, $out_file_prefix, $sort)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/calllogs/export",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$data = [
			"conditions" => $conditions,
			"fields" => $fields,
			"out_file_prefix" => $out_file_prefix,
			"sort" => $sort

		];

		return $this->requestApi($conf, $data);
		
	}

	public function getCsv($file_name, $callback_host)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/calllogs/export/file",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$data = [
			"file_name" => $file_name,
			"callback_host" => $callback_host

		];

		return $this->requestApi($conf, $data);
		
	}

	public function getAggregatedCallLogs($conditions)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/calllogs/aggregate",
			'requestType' => 'POST',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$data = $conditions;

		return $this->requestApi($conf, $data);
	}

	public function updateMany($conditions, $update)
	{
		$conf = [
			'url' => "{$this->apiUrl}/api/calllogs/update",
			'requestType' => 'PUT',
			'headers' => [
				"auth-token: {$this->apiAccessToken}",
				"Content-Type: application/json"
			]

		];
		$data = [
			"conditions" => $conditions,
			"set" => $update
		];

		return $this->requestApi($conf, $data);
	}

	private function requestApi($params, $data)
	{
        if (!empty($params['url'])) {
            if (empty($params['headers'])) {
                $params['headers'] = ['Content-Type: application/x-www-form-urlencoded'];
            }
            // Open connection
            $ch = curl_init();

            // Set the URL, number of POST vars, POST data
            if (!empty($params['requestType'])) {
                // if (!empty($data)) {
                    if (!empty($params['requestType']) && $params['requestType'] == 'GET') {
                		$postData = http_build_query($data);
                        // Check given url and apend.
                        $url = $params['url'] . '?' . $postData;
                        curl_setopt($ch, CURLOPT_URL, $url);
                    } else if (!empty($params['requestType']) && $params['requestType'] == 'POST') {
                    	$postData = json_encode($data);
                        curl_setopt($ch, CURLOPT_URL, $params['url']);
                        curl_setopt($ch, CURLOPT_POST, count($postData));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    } else if (!empty($params['requestType']) && $params['requestType'] == 'PUT') {
                    	$postData = json_encode($data);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                        curl_setopt($ch, CURLOPT_URL, $params['url']);
                        curl_setopt($ch, CURLOPT_POST, count($postData));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    } else if (!empty($params['requestType']) && $params['requestType'] == "DELETE") {

                    	$postData = json_encode($data);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($ch, CURLOPT_URL, $params['url']);
                        curl_setopt($ch, CURLOPT_POST, count($postData));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    }
                // }
            } else {
                curl_setopt($ch, CURLOPT_URL, $params['url']);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $params['headers']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Execute post
            $curl_response = curl_exec($ch);
            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($response_code == 400) {
            	$authToken = $this->getAccessToken();
            	curl_setopt($ch, CURLOPT_HTTPHEADER, array("auth-token: $authToken",
                "Content-type: application/json"));
                $curl_response = curl_exec($ch);
                $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }
            
            
            // Close connection
            curl_close($ch);

            // if($response_code == 200) {

            // 	$parsed_response = json_decode($curl_response, true);
            // 	if($parsed_response['status']) {
            // 		return $parsed_response['data'];
            // 	} else {
            // 		return [];
            // 	}
            // } else {
            // 	return [];
            // }

            return [
                'response_code' => $response_code,
                'response' => $curl_response
            ];
        }
    }
}

