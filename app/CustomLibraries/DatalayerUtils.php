<?php

namespace App\CustomLibraries;

use App\CustomLibraries\DatalayerClient;

/**
 * DatalayerClient
 * 
 * @brief Waybeo Datalayer Client Library
 * @description Waybeo Datalayer Client Library
 * @todo update the credentials in .env file or to a constant 
 * @version  v1.0
 * 
 */
class DatalayerUtils
{
	public static function paginate($currentPage = 1, $itemsPerPage, $conditions)
	{
		$dl = new DatalayerClient();
                $curl_response = $dl->getPaginatedCallLogs($currentPage, $itemsPerPage, $conditions);

                if($curl_response['response_code'] == 200) {

                	$parsed_response = json_decode($curl_response['response'], true);
                	if($parsed_response['status']) {
                		return $parsed_response['data'];
                	} else {
                		return [];
                	}
                } else {
                	return [];
                }
	}

	public static function aggregate($aggregateConditions)
	{
		$dl = new DatalayerClient();
		$curl_response = $dl->getAggregatedCallLogs($aggregateConditions);

		if($curl_response['response_code'] == 200) {

                	$parsed_response = json_decode($curl_response['response'], true);
                	if($parsed_response['status']) {
                		return $parsed_response['data'];
                	} else {
                		return ['result' => []];
                	}
                } else {
                	return ['result' => []];
                }
	}

	public static function callDetails($id)
	{
		$dl = new DatalayerClient();
		$curl_response = $dl->getCallLog($id);

		if($curl_response['response_code'] == 200) {

                	$parsed_response = json_decode($curl_response['response'], true);
                	if($parsed_response['status']) {
                		return $parsed_response['data'];
                	} else {
                		return [];
                	}
                } else {
                	return [];
                }
	}

        public static function count($conditions)
        {
                $dl = new DatalayerClient();
                $curl_response = $dl->getCount($conditions);

                if($curl_response['response_code'] == 200) {

                        $parsed_response = json_decode($curl_response['response'], true);
                        if($parsed_response['status']) {
                                return $parsed_response['data'];
                        } else {
                                return [];
                        }
                } else {
                        return [];
                }
        }

        public static function export($conditions, $fields, $out_file_prefix, $sort)
        {
                $dl = new DatalayerClient();
                $curl_response = $dl->export($conditions, $fields, $out_file_prefix, $sort);

                if($curl_response['response_code'] == 200) {

                        $parsed_response = json_decode($curl_response['response'], true);
                        if($parsed_response['status']) {
                                return $parsed_response['data'];
                        } else {
                                return [];
                        }
                } else {
                        return [];
                }
        }

        public static function getCsv($file_name, $callback_host)
        {
                $dl = new DatalayerClient();
                $curl_response = $dl->getCsv($file_name, $callback_host);

                if($curl_response['response_code'] == 200) {

                        $parsed_response = json_decode($curl_response['response'], true);
                        if($parsed_response['status']) {
                                return $parsed_response['data'];
                        } else {
                                return [];
                        }
                } else {
                        return [];
                }
        }

        public static function createCallLog($data)
        {
                $dl = new DatalayerClient();
                $curl_response = $dl->createCallLog($data);

                if($curl_response['response_code'] == 200) {

                        $parsed_response = json_decode($curl_response['response'], true);
                        if($parsed_response['status']) {
                                return $parsed_response['data'];
                        } else {
                                return [];
                        }
                } else {
                        return [];
                }
        }
        public static function updateCallLog($id,$data)
        {
                $dl = new DatalayerClient();
                $curl_response = $dl->updateCallLog($id,$data);

                if($curl_response['response_code'] == 200) {

                        $parsed_response = json_decode($curl_response['response'], true);
                        if($parsed_response['status']) {
                                return $parsed_response['data'];
                        } else {
                                return [];
                        }
                } else {
                        return [];
                }
        }

        public static function updateMany($conditions,$data)
        {
                $dl = new DatalayerClient();
                $curl_response = $dl->updateMany($conditions,$data);

                if($curl_response['response_code'] == 200) {

                        $parsed_response = json_decode($curl_response['response'], true);
                        if($parsed_response['status']) {
                                return $parsed_response['data'];
                        } else {
                                return [];
                        }
                } else {
                        return [];
                }
        }
}