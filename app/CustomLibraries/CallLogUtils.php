<?php

namespace App\CustomLibraries;

use App\CustomLibraries\Calllog;
use App\CustomLibraries\MongoPagination;
use App\CustomLibraries\Utils\DomUtils;
use App\CustomLibraries\Utils\RsmUtils;
use App\CustomLibraries\Utils\DbUtils;
use App\CustomLibraries\Utils\Rmutils;
use MongoId;

/**
 * Description of CallLogUtils Class
 * @brief CallLogUtils class for getting custom calllog collection
 * @description Mongo class for connnecting to calllog collection 
 * @author Jaison John <jaison.john@waybeo.com>
 * @date Wedenesday, 2017 July 20
 * @example CallLogUtils::getCalllog();
 */

class CallLogUtils
{


    public static function getCalllogs($params, $fields = NULL, $itemsPerPage = NULL, $export = NULL)
    {
        $modelCallLog = new Calllog();
        $pagination = new MongoPagination($modelCallLog->mongoDb);
        $conditions = array();
        if ($fields == NULL) {
            $fields = [];
        } elseif (is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_fill_keys($fields, 1);
        }
        $conditions['CallId'] = array('$exists' => 1);
        $conditions = self::getConditions($params);
        // dd(json_encode($conditions));
        // dd($conditions);
        // if ($params['State'] != NULL) {
        //     $conditions = array_merge($conditions, array('State' => (string)$params['State']));
        // }
        // if ($params['City'] != NULL) {
        //     $conditions = array_merge($conditions, array('City' => (string)$params['City']));
        // }
        // if ($params['DealerId'] != NULL) {
        //     $conditions = array_merge($conditions, array('DealerId' => (integer)$params['DealerId']));
        // }
        // if ($params['Status'] != NULL) {
        //     $conditions = array_merge($conditions, array('Status' => (string)$params['Status']));
        // }
        // if ($params['CallerId'] != NULL){
        //     $conditions = array_merge($conditions,  array('CustomerNumber' =>  (integer)$params['CallerId']));
        // }
        // if ($params['VirtualNumberType'] != NULL) {
        //     $conditions = array_merge($conditions, array('VirtualNumberType' => (string)$params['VirtualNumberType']));
        // }
        // if ($params['DbId'] != NULL) {
        //     $dbDealerIds = DbUtils::getDealerIds($params['DbId']);
        //     $conditions = array_merge($conditions, ['DealerId' => ['$in' => array_map('intval', $dbDealerIds)]]);
        // }
        // if($params['date_to'] != NULL && $params['date_from'] == NULL) {
        //     $to = $params['date_to'];
        //     $conditions = array_merge($conditions, array('Date' => array('$lte' => $params['date_to'])));
        // }
        // if ($params['date_to'] == NULL && $params['date_from'] != NULL){
        //     $from = $params['date_from'];
        //     $to = date('Y-m-d');
        //     $conditions = array_merge($conditions, array('Date' => array('$gte' => $from, '$lte' => $to)));
        // }
        // if ($params['date_to'] != NULL && $params['date_from'] != NULL){
        //     list($Y, $m, $d) = explode('-', $params['date_to']);
        //     $to = date('Y-m-d', strtotime($params['date_to']));
        //     $conditions = array_merge ($conditions, array('Date' => array('$gte' => date('Y-m-d', strtotime($params['date_from'])), '$lte' => $to)));
        // }

        if ($export == NULL) {
            $currentPage = $params['page'];
            $pagination->setQuery(array(
                '#collection'   =>  'calllogs',
                '#find'         =>  $conditions,
                '#sort'         =>  array("_id" => -1),
                '#fields'       =>  $fields,
            ), $currentPage, $itemsPerPage);
            $dataSet    = $pagination->paginate();

            $page_links = $pagination->getPageLinks($count = 9);


        
            //return array($conditions, $page_links,$count);
            return array($dataSet, $page_links, $count);
        } elseif ($export == 1) {
            #CALLLOG QUERY SPLITTING STARTS#
            $count = $modelCallLog->mongoStr->find($conditions)->count();
            $recordsCount = $count;
            $recordsChunk = 20000;
            if (($recordsCount % $recordsChunk) > 0) {
                $recordsCount = $recordsCount + $recordsChunk;
            }
            $CURSOR = [];
            $_lastMongoId = null;
            for ($i = $recordsChunk; $i <= $recordsCount; $i += $recordsChunk) {

                if (!empty($_lastMongoId)) {
                    $conditions = array_merge($conditions, array('_id' => array('$lt' => new MongoId($_lastMongoId))));
                }
                // var_dump(json_encode($fields));exit;

                $cursor = $modelCallLog->mongoStr->find($conditions, $fields)
                    ->sort(array("_id" => -1))
                    ->limit($recordsChunk);

                $cursor = iterator_to_array($cursor);
                $end = end($cursor);
                $_lastMongoId = (string) $end["_id"];
                array_push($CURSOR, $cursor);
            }


            $RESPONSE = [];
            foreach ($CURSOR as $KEY => $VALUE) {
                $RESPONSE = array_merge($RESPONSE, $VALUE);
            }

            return $RESPONSE;
            #CALLLOG QUERY SPLITTING ENDS#

            //            $cursor = $modelCallLog->mongoStr->find($conditions, $_fields)
            //            ->sort(array("_id" => -1));
            // if (isset($_limit) && $_limit > 0)
            //     $cursor->limit($_limit);
            // return $cursor;
        }
    }

    public static function getCallDetails($callid, $userId = null)
    {
        $calllogCollection = new Calllog();
        $condition = array('_id' => new MongoId($callid));
        if (!empty($userId)) {
            $condition['userid'] = $userId;
        }
        return $calllogCollection->mongoStr->findOne($condition);
    }

    public static function fetchCallsStatusByDay($query)
    {
        $match =  self::getConditions($query);
        $group = [
            '_id' => [
                'Month' => '$Date'
            ],
            'Connected' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Connected']
                        ], 1, 0
                    ]
                ]
            ],
            'Missed' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Missed']
                        ], 1, 0
                    ]
                ]
            ],
            'Total Calls' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$ne' => ['$CustomerNumber', '0']
                        ], 1, 0
                    ]
                ]
            ],
        ];
        $sort = [
            '_id' => 1
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        );
        return $cursor['result'];
    }

    public static function getMatchQueryForChart($query)
    {
        $match = ['CallId' => ['$exists' => true]];


        if (!empty($query['DealerId'])) {
            $match['DealerId'] = (int) $query['DealerId'];
        }
        if (!empty($query['date_from'])) {
            $match['Date']['$gte'] = $query['date_from'];
        }
        if (!empty($query['date_to'])) {
            $match['Date']['$lte'] = $query['date_to'];
        }
        if (!empty($query['Status'])) {
            $match['Status'] = (string) $query['Status'];
        }
        if (!empty($query['CallerId'])) {
            $match['CustomerNumber'] = (int) $query['CallerId'];
        }
        if (!empty($query['State'])) {
            $match['State'] = (string) $query['State'];
        }
        if (!empty($query['City'])) {
            $match['City'] = (string) $query['City'];
        }
        if (!empty($query['VirtualNumberType'])) {
            $match['VirtualNumberType'] = (string) $query['VirtualNumberType'];
        }
        if (!empty($query['DbId'])) {
            $dbDealerIds = DbUtils::getDealerIds($query['DbId']);
            $match = array_merge($match, ['DealerId' => ['$in' =>  array_map('intval', $dbDealerIds)]]);
        }
        // @todo update this code, copying old code only because

        return $match;
    }

    public static function fetchCallDurationAverage($query)
    {
        $match = self::getConditions($query);
        $group = [
            '_id' => '',
            'TotalDuration' => ['$sum' => '$TotalDuration'],
            'ConversationDuration' => ['$sum' => '$ConversationDuration'],
            'IVRDuration' => ['$sum' => '$IVRDuration'],
            'RingDuration' => ['$sum' => '$RingDuration'],
            'Count' => ['$sum' => 1],
            'TotalAnsweredCalls' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Connected']
                        ], 1, 0
                    ]
                ]
            ],
            'Missed' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Missed']
                        ], 1, 0
                    ]
                ]
            ],
            'IVR Drop' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'IVR Drop']
                        ], 1, 0
                    ]
                ]
            ],
            'Offline' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Offline']
                        ], 1, 0
                    ]
                ]
            ]
        ];
        $sort = [
            '_id' => 1
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        );
        return $cursor['result'];
    }

    public static function getFilterParams($params)
    {
        if (empty($params['page'])) {
            $params['page'] = 1;
        }
        if (empty($params['Type'])) {
            $params['Type'] = NULL;
        }
        if (empty($params['CallerId'])) {
            $params['CallerId'] = NULL;
        }
        if (empty($params['DealerId'])) {
            $params['DealerId'] = NULL;
        }
        if (empty($params['RsmId'])) {
            $params['RsmId'] = NULL;
        }
        if (empty($params['DomId'])) {
            $params['DomId'] = NULL;
        }
        if (empty($params['date_from'])) {
            $params['date_from'] = date('Y-m-d', strtotime(date('Y-m-d') . ' -14 days'));
        }
        if (empty($params['date_to'])) {
            $params['date_to'] = NULL;
        }
        if (empty($params['PandaCode'])) {
            $params['PandaCode'] = NULL;
        }
        if (empty($params['Region'])) {
            $params['Region'] = NULL;
        }
        if (empty($params['Location'])) {
            $params['Location'] = NULL;
        }
        if (empty($params['TrackingNumber'])) {
            $params['TrackingNumber'] = NULL;
        }
        if (empty($params['IVRDuration'])) {
            $params['IVRDuration'] = NULL;
        }
        if (empty($params['Status'])) {
            $params['Status'] = NULL;
        }
        if (empty($params['TotalDuration'])) {
            $params['TotalDuration'] = NULL;
        }
        if (empty($params['UniqueId'])) {
            $params['UniqueId'] = NULL;
        }
        if (empty($params['callType'])) {
            $params['callType'] = NULL;
        }
        if (empty($params['State'])) {
            $params['State'] = NULL;
        }
        if (empty($params['Unique'])) {
            $params['Unique'] = NULL;
        }
        if (empty($params['State'])) {
            $params['State'] = NULL;
        }
        if (empty($params['City'])) {
            $params['City'] = NULL;
        }

        if (empty($params['DbId'])) {
            $params['DbId'] = NULL;
        }
        if (empty($params['VirtualNumberType'])) {
            $params['VirtualNumberType'] = NULL;
        }
        return $params;
    }

    public static function mapFilterFields($fields)
    {
        // $fields = str_replace('CallStartTime', 'DateTime', $fields);
        $fields = str_replace('CallRecording', 'CallRecordUrl', $fields);
        $fields = str_replace(',Duration', ',ConversationDuration', $fields);
        $fields = str_replace('ConnectedTo', 'AgentNumber', $fields);
        $fields = str_replace('BusyCallees', 'BusyCalleesStr', $fields);
        $fields = str_replace('StoreId', 'StoreCode', $fields);
        $fields = str_replace('Type', 'StoreType', $fields);
        $fields .= ',TotalDuration';
        return $fields;
    }

    public static function getDailyCallReport()
    {
        $match = ['Date' => date('Y-m-d', strtotime("-1 days"))];
        $group = [
            '_id' => '$Date',
            'answeredCalls' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Call Answered']
                        ], 1, 0
                    ]
                ]
            ],
            'totalCalls' => [
                '$sum' => 1
            ],
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group]
        );
        return $cursor['result'][0];
    }

    public static function getCalllogsCount($params)
    {
        $filter_conditions = CallLogUtils::getConditions($params);

        $modelCallLog = new Calllog();
        $count = $modelCallLog->mongoStr->find($filter_conditions)->count();
        if ($count > 50000) {
            return true;
        } else {
            return true;
        }
    }

    public static function getFilterConditions($params)
    {
        $conditions = array();
        $fields = '';
        $conditions['CallId'] = array('$exists' => 1);

        if (!empty($params['Fields'])) {
            $fields = $params['Fields'];
        }
        if ($params['State'] != NULL) {
            $conditions = array_merge($conditions, array('State' => (string) $params['State']));
        }
        if ($params['Unique'] != NULL) {
            $conditions = array_merge($conditions, array('RepeatedCaller' => 0));
        }
        if ($params['DomId'] != NULL) {
            $domDealerIds = DomUtils::getDealerIds($params['DomId']);
            $conditions = array_merge($conditions, ['DealerId' => ['$in' =>  array_map('strval', $domDealerIds)]]);
        }
        if ($params['RsmId'] != NULL) {
            $rsmDealerIds = RsmUtils::getDealerIds($params['RsmId']);
            $conditions = array_merge($conditions, ['DealerId' => ['$in' => array_map('strval', $rsmDealerIds)]]);
        }
        if ($params['DealerId'] != NULL) {
            $conditions = array_merge($conditions, array('DealerId' => (string) $params['DealerId']));
        }
        if ($params['Type'] != NULL) {
            $conditions = array_merge($conditions, array('Type' => (string) $params['Type']));
        }
        if ($params['CallerId'] != NULL) {
            $conditions = array_merge($conditions,  array('CustomerNumber' =>  (int) $params['CallerId']));
        }
        if ($params['date_to'] != NULL && $params['date_from'] != NULL) {
            list($Y, $m, $d) = explode('-', $params['date_to']);
            $params['date_to'] = date('Y-m-d', strtotime($params['date_to']));
            $conditions = array_merge($conditions, array('Date' => array('$gte' => $params['date_from'], '$lte' => $params['date_to'])));
        }
        if ($params['date_to'] != NULL && $params['date_from'] == NULL) {
            $params['date_to'] = $params['date_to'];
            $conditions = array_merge($conditions, array('Date' => array('$lte' => $params['date_to'])));
        }
        if ($params['date_to'] == NULL && $params['date_from'] != NULL) {
            $params['date_from'] = $params['date_from'];
            $params['date_to'] = date('Y-m-d');
            $conditions = array_merge($conditions, array('Date' => array('$gte' => $params['date_from'], '$lte' => $params['date_to'])));
        }
        if ($params['Status'] != NULL) {
            $conditions = array_merge($conditions, array('Status' => (string) $params['Status']));
        }
        if ($params['TotalDuration'] != NULL) {
            $conditions = array_merge($conditions, array('TotalDuration' => $params['TotalDuration']));
        }
        if ($params['State'] != NULL) {
            $conditions = array_merge($conditions, array('State' => (string) $params['State']));
        }
        if (isset($params['City']) && $params['City'] != NULL) {
            $conditions = array_merge($conditions, array('City' => (string) $params['City']));
        }
        if (isset($params['Region']) && $params['Region'] != NULL) {
            $conditions = array_merge($conditions, array('Region' => (string) $params['Region']));
        }
        if ($params['callType'] != NULL) {
            if ($params['callType'] == '1') {
                $conditions = array_merge($conditions, array('IvrLog.0.1' => 'sales'));
            } elseif ($params['callType'] == '2') {
                $conditions = array_merge($conditions, array('IvrLog.0.2' => 'service'));
            } elseif ($params['callType'] == '3') {
                $conditions = array_merge($conditions, array('IvrLog.0.3' => 'other'));
            }
        }

        if ($params['TotalDuration'] != NULL) {
            if ($params['TotalDuration'] == '6') {
                $conditions = array_merge($conditions, array('TotalDuration' => array('$gt' => 300)));
            } else {
                if ($params['TotalDuration'] == '1') {
                    $_low = -1;
                    $_high = 60;
                } elseif ($params['TotalDuration'] == '3') {
                    $_low = 60;
                    $_high = 180;
                } elseif ($params['TotalDuration'] == '5') {
                    $_low = 180;
                    $_high = 300;
                }
                $conditions = array_merge($conditions, array('TotalDuration' => array('$gt' => $_low, '$lte' => $_high)));
            }
        }

        return [
            'conditions' => $conditions,
            'fields' => $fields
        ];
    }

    public static function getFilterConditionsForExport($params)
    {
        $conditions = array();
        $fields = '';
        $conditions['CallId'] = array('$exists' => 1);

        if (!empty($params['Fields'])) {
            $fields = $params['Fields'];
        }
        if (!empty($params['State'])) {
            $conditions = array_merge($conditions, array('State' => (string) $params['State']));
        }
        if (!empty($params['Unique'])) {
            $conditions = array_merge($conditions, array('RepeatedCaller' => 0));
        }
        if (!empty($params['DomId'])) {
            $domDealerIds = DomUtils::getDealerIds($params['DomId']);
            $conditions = array_merge($conditions, ['DealerId' => ['$in' =>  array_map('strval', $domDealerIds)]]);
        }
        if (!empty($params['RsmId'])) {
            $rsmDealerIds = RsmUtils::getDealerIds($params['RsmId']);
            $conditions = array_merge($conditions, ['DealerId' => ['$in' => array_map('strval', $rsmDealerIds)]]);
        }
        if (!empty($params['DealerId'])) {
            $conditions = array_merge($conditions, array('DealerId' => (int) $params['DealerId']));
        }
        if (!empty($params['Type'])) {
            $conditions = array_merge($conditions, array('Type' => (string) $params['Type']));
        }
        if (!empty($params['CallerId'])) {
            $conditions = array_merge($conditions,  array('CustomerNumber' =>  (int) $params['CallerId']));
        }
        if (!empty($params['VirtualNumberType'])) {
            $conditions = array_merge($conditions, array('VirtualNumberType' => (string) $params['VirtualNumberType']));
        }
        if (!empty($params['DbId'])) {
            $dbDealerIds = DbUtils::getDealerIds($params['DbId']);
            $conditions = array_merge($conditions, ['DealerId' => ['$in' => array_map('intval', $dbDealerIds)]]);
        }
        if ($params['date_to'] != NULL && $params['date_from'] != NULL) {
            list($Y, $m, $d) = explode('-', $params['date_to']);
            $params['date_to'] = date('Y-m-d', strtotime($params['date_to']));
            $conditions = array_merge($conditions, array('Date' => array('$gte' => "'" . $params['date_from'] . "'", '$lte' => "'" . $params['date_to'] . "'")));
        }
        if ($params['date_to'] != NULL && $params['date_from'] == NULL) {
            $params['date_to'] = $params['date_to'];
            $conditions = array_merge($conditions, array('Date' => array('$lte' => "'" . $params['date_to'] . "'")));
        }
        if ($params['date_to'] == NULL && $params['date_from'] != NULL) {
            $params['date_from'] = $params['date_from'];
            $params['date_to'] = date('Y-m-d');
            $conditions = array_merge($conditions, array('Date' => array('$gte' => "'" . $params['date_from'] . "'", '$lte' => "'" . $params['date_to'] . "'")));
        }
        if (!empty($params['Status'])) {
            $conditions = array_merge($conditions, array('Status' => (string) $params['Status']));
        }
        if (!empty($params['TotalDuration'])) {
            $conditions = array_merge($conditions, array('TotalDuration' => $params['TotalDuration']));
        }
        if (!empty($params['City'])) {
            $conditions = array_merge($conditions, array('City' => (string) $params['City']));
        }
        if (!empty($params['Region'])) {
            $conditions = array_merge($conditions, array('Region' => (string) $params['Region']));
        }
        if (!empty($params['Location'])) {
            $conditions = array_merge($conditions, array('Location' => (string) $params['Location']));
        }
        if (!empty($params['StateId']) ) {
            $conditions = array_merge($conditions, array('StateId' => (int)$params['StateId']));
        }
        if (!empty($params['CityId'])) {
            $conditions = array_merge($conditions, array('CityId' => (int)$params['CityId']));
        }
        if (!empty($params['StoreId'])) {
            $conditions = array_merge($conditions, array('StoreId' => (int) $params['StoreId']));
        }
        if (!empty($params['callType'])) {
            if ($params['callType'] == '1') {
                $conditions = array_merge($conditions, array('IvrLog.0.1' => 'sales'));
            } elseif ($params['callType'] == '2') {
                $conditions = array_merge($conditions, array('IvrLog.0.2' => 'service'));
            } elseif ($params['callType'] == '3') {
                $conditions = array_merge($conditions, array('IvrLog.0.3' => 'other'));
            }
        }

        if (!empty($params['TotalDuration'])) {
            if ($params['TotalDuration'] == '6') {
                $conditions = array_merge($conditions, array('TotalDuration' => array('$gt' => 300)));
            } else {
                if ($params['TotalDuration'] == '1') {
                    $_low = -1;
                    $_high = 60;
                } elseif ($params['TotalDuration'] == '3') {
                    $_low = 60;
                    $_high = 180;
                } elseif ($params['TotalDuration'] == '5') {
                    $_low = 180;
                    $_high = 300;
                }
                $conditions = array_merge($conditions, array('TotalDuration' => array('$gt' => $_low, '$lte' => $_high)));
            }
        }

        return [
            'conditions' => $conditions,
            'fields' => $fields
        ];
    }

    public static function getPaginatedAgencyCalllogs(
        $itemsPerPage = 10,
        $currentPage = 1,
        $params,
        $_fields
    ) {
        $modelCallLog = new Calllog();
        $pagination = new MongoPagination($modelCallLog->mongoDb);

        $conditions = array();
        $fields = array();

        if (!empty($params['Type'])) {
            $conditions = array_merge($conditions, array('Type' => $params['Type']));
        } else {
            $conditions = array_merge($conditions, array('Type' => ['$in' => ["SC_DBM", "SC_VSERVE"]]));
        }

        if (!empty($params['date_to']) && empty($params['date_from'])) {
            $_to = $params['date_to'] . ' 23:59:59';
            $conditions = array_merge($conditions, array('DateTime' => array('$lte' => $params['date_to'])));
        }
        if (empty($params['date_to']) && !empty($params['date_from'])) {
            $_from = $params['date_from'] . ' 00:00:00';
            $_to = date('Y-m-d') . ' 23:59:59';
            $conditions = array_merge($conditions, array('DateTime' => array('$gte' => $_from, '$lte' => $_to)));
        }
        if (!empty($params['date_to']) && !empty($params['date_from'])) {
            $_to = date('Y-m-d', strtotime($params['date_to']));
            $conditions = array_merge($conditions, array('DateTime' => array('$gte' => date('Y-m-d', strtotime($params['date_from'])) . ' 00:00:00', '$lte' => $_to . ' 23:59:59')));
        }

        $pagination->setQuery(array(
            '#collection'   =>  'ford_calllogs',
            '#find'         =>  $conditions,
            '#sort'         =>  array("_id" => -1),
            '#fields'       =>  $_fields,
        ), $currentPage, $itemsPerPage);
        $dataSet    = $pagination->paginate();
        $page_links = $pagination->getPageLinks($count = 9);

        $count = array();
        $count['Total'] = $dataSet['totalItems'];
        $count['Call Answered'] = 0;
        $count['Call Abandoned'] = 0;
        $count['Missed Call'] = 0;
        if (isset($conditions['Status']) && (strlen($conditions['Status']) > 0)) {
            $count[$conditions['Status']] = $count['Total'];
        } else {
            $group = ['_id' => '$Status', 'count' => ['$sum' => 1]];
            $calllogCollection = new Calllog();
            $cursor = $calllogCollection->mongoStr->aggregate(
                ['$match' => $conditions],
                ['$group' => $group]
            );
            foreach ($cursor['result'] as $res) {
                $count[$res['_id']] = $res['count'];
            }
        }
        return array($dataSet, $page_links, $count);
    }

    public static function getAgencyCalllogs($params, $_fields)
    {

        $conditions = array();
        $fields = array();

        if (!empty($params['Type'])) {
            $conditions = array_merge($conditions, array('Type' => (string) $params['Type']));
        } else {
            $conditions = array_merge($conditions, array('Type' => ['$in' => ["SC_DBM", "SC_VSERVE"]]));
        }
        if (!empty($params['date_to']) && empty($params['date_from'])) {
            $_to = $params['date_to'];
            $conditions = array_merge($conditions, array('Date' => array('$lte' => $_to)));
        }
        if (empty($params['date_to']) && !empty($params['date_from'])) {
            $_from = $params['date_from'];
            $_to = date('Y-m-d');
            $conditions = array_merge($conditions, array('Date' => array('$gte' => $_from, '$lte' => $_to)));
        }
        if (!empty($params['date_to']) && !empty($params['date_from'])) {
            $_to = date('Y-m-d', strtotime($params['date_to']));
            $conditions = array_merge($conditions, array('Date' => array('$gte' => $params['date_from'], '$lte' => $_to)));
        }

        $modelCallLog = new Calllog();
        #CALLLOG QUERY SPLITTING STARTS#
        $count = $modelCallLog->mongoStr->find($conditions)->count();
        $recordsCount = $count;
        $recordsChunk = 20000;
        if ($recordsCount < $recordsChunk) {
            $recordsChunk = $recordsCount;
        }
        if (($recordsCount % $recordsChunk) > 0) {
            $recordsCount = $recordsCount + $recordsChunk;
        }
        $CURSOR = [];
        $_lastMongoId = null;
        for ($i = $recordsChunk; $i <= $recordsCount; $i += $recordsChunk) {

            if (!empty($_lastMongoId)) {
                $conditions = array_merge($conditions, array('_id' => array('$lt' => new MongoId($_lastMongoId))));
            }
            $cursor = $modelCallLog->mongoStr->find($conditions, $_fields)
                ->sort(array("_id" => -1))
                ->limit($recordsChunk);

            $cursor = iterator_to_array($cursor);
            $end = end($cursor);
            $_lastMongoId = (string) $end["_id"];
            array_push($CURSOR, $cursor);
        }

        $RESPONSE = [];
        foreach ($CURSOR as $KEY => $VALUE) {
            $RESPONSE = array_merge($RESPONSE, $VALUE);
        }

        return $RESPONSE;
        #CALLLOG QUERY SPLITTING ENDS#
    }

    public static function updateLivAiCallTags($LivAiSessionId, $tags)
    {
        $modelCallLog = new Calllog();
        $sessionIdCheck = ['LivAiSessionId' => $LivAiSessionId, 'Date' => ['$gte' => '2018-09-01']];
        $cursor = $modelCallLog->mongoStr->find($sessionIdCheck);
        $data = $cursor->getNext();
        if (!empty($data)) {
            $condition = ['LivAiSessionId' => $LivAiSessionId, 'Date' => ['$gte' => '2018-09-01']];
            $count = $modelCallLog->mongoStr->update($condition, [
                '$set' => [
                    'CarTags' => $tags['carTags'],
                    'KeywordTags' => $tags['keywordTags'],
                    'CarTagTexts' => $tags['carTagTexts'],
                    'KeywordTagTexts' => $tags['keywordTagTexts'],
                    'AiTags' => $tags['tags'],
                    'AiType' => $tags['aiType'],
                    'AiProcessed' => empty($tags['carTagTexts']) ? 0 : 1
                ]
            ]);
        } else {
            $condition = ['DealerLivAiSessionId' => $LivAiSessionId, 'Date' => ['$gte' => '2018-11-01']];
            $count = $modelCallLog->mongoStr->update($condition, [
                '$set' => [
                    'CarTags' => $tags['carTags'],
                    'CarTagTexts' => $tags['carTagTexts'],
                    'DealerCarTags' => $tags['carTags'],
                    'DealerCarTagTexts' => $tags['carTagTexts'],
                    'DealerAiTags' => $tags['tags'],
                    'AiProcessed' => empty($tags['carTagTexts']) ? 0 : 1
                ]
            ]);
        }
    }

    public static function getDepartmentViseCallCount($data)
    {
        $match = [];
        if (isset($data['dealerId'])) {
            $match['DealerId'] = (string) $data['dealerId'];
        } elseif (isset($data['rsmId'])) {
            $rsmDealerIds = RsmUtils::getDealerIds($data['rsmId']);
            $match = array_merge($match, ['DealerId' => ['$in' => array_map('strval', $rsmDealerIds)]]);
        } elseif (isset($data['domId'])) {
            $domDealerIds = DomUtils::getDealerIds($data['domId']);
            $match = array_merge($match, ['DealerId' => ['$in' =>  array_map('strval', $domDealerIds)]]);
        }
        if (empty($data['from'])) {
            $match['Date'] = date('Y-m-d');
        } elseif (empty($data['to'])) {
            $match['Date'] = ['$gte' => $data['from']];
        } else {
            $match['Date'] = ['$gte' => $data['from'], '$lte' => $data['to']];
        }
        $group = [
            '_id' => '$IvrLog',
            'count' => [
                '$sum' => 1
            ]
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group]
        );
        $response = [];
        foreach ($cursor['result'] as $result) {
            if (empty($result['_id'])) {
                $response['no_department'] = $result['count'];
            } elseif (isset($result['_id'][0][1])) {
                $response['sales'] = $result['count'];
            } elseif (isset($result['_id'][0][2])) {
                $response['service'] = $result['count'];
            } elseif (isset($result['_id'][0][3])) {
                $response['others'] = $result['count'];
            }
        }
        return $response;
    }

    public static function getCallStatusViseCount($data)
    {
        $match = [];
        if (isset($data['dealerId'])) {
            $match['DealerId'] = (string) $data['dealerId'];
        } elseif (isset($data['rsmId'])) {
            $rsmDealerIds = RsmUtils::getDealerIds($data['rsmId']);
            $match = array_merge($match, ['DealerId' => ['$in' => array_map('strval', $rsmDealerIds)]]);
        } elseif (isset($data['domId'])) {
            $domDealerIds = DomUtils::getDealerIds($data['domId']);
            $match = array_merge($match, ['DealerId' => ['$in' =>  array_map('strval', $domDealerIds)]]);
        }
        if (empty($data['from'])) {
            $match['Date'] = date('Y-m-d');
        } elseif (empty($data['to'])) {
            $match['Date'] = ['$gte' => $data['from']];
        } else {
            $match['Date'] = ['$gte' => $data['from'], '$lte' => $data['to']];
        }
        if (!empty($data['department'])) {
            if ($data['department'] == 'sales') {
                $match['IvrLog'][0][1] = $data['department'];
            } elseif ($data['department'] == 'service') {
                $match['IvrLog'][0][2] = $data['department'];
            } else {
                $match['IvrLog'][0][3] = $data['department'];
            }
        }
        $group = [
            '_id' => '$Status',
            'count' => [
                '$sum' => 1
            ]
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group]
        );
        $response = [];
        foreach ($cursor['result'] as $result) {
            if ($result['_id'] == 'Call Answered') {
                $response['answered'] = $result['count'];
            } elseif ($result['_id'] == 'Call Abandoned') {
                $response['abandoned'] = $result['count'];
            } elseif ($result['_id'] == 'Missed Call') {
                $response['missed'] = $result['count'];
            }
        }
        return $response;
    }

    public static function getDealerAgentAnswerCount($dealerId, $department, $from = NULL, $to = NULL)
    {
        $match = [
            'DealerId' => (string) $dealerId,
            'Status' => [
                '$in' => ['Call Answered', 'Missed Call']
            ]
        ];
        if (empty($from)) {
            $match['Date'] = date('Y-m-d');
        } elseif (empty($to)) {
            $match['Date'] = ['$gte' => $from];
        } else {
            $match['Date'] = ['$gte' => $from, '$lte' => $to];
        }
        if (!empty($department)) {
            if ($department == 'sales') {
                $match['IvrLog'][0][1] = $department;
            } elseif ($department == 'service') {
                $match['IvrLog'][0][2] = $department;
            } else {
                $match['IvrLog'][0][3] = $department;
            }
        }
        $group = [
            '_id' => '$AgentNumber',
            'answered_count' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Call Answered']
                        ], 1, 0
                    ]
                ]
            ],
            'missed_count' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Missed Call']
                        ], 1, 0
                    ]
                ]
            ]
        ];
        $sort = [
            'answered_count' => -1
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        );
        $response = [
            'total_answered' => 0,
            'total_missed' => 0
        ];
        foreach ($cursor['result'] as $result) {
            if (!empty($result['_id'])) {
                $response[$result['_id']] = $result['answered_count'];
            }
            $response['total_answered'] += $result['answered_count'];
            $response['total_missed'] += $result['missed_count'];
        }
        return $response;
    }

    public static function getDealerMissedCount($data)
    {
        $match = [
            'Status' => [
                '$ne' => 'Call Abandoned'
            ]
        ];
        if (isset($data['rsmId'])) {
            $rsmDealerIds = RsmUtils::getDealerIds($data['rsmId']);
            $match = array_merge($match, ['DealerId' => ['$in' => array_map('strval', $rsmDealerIds)]]);
        } elseif (isset($data['domId'])) {
            $domDealerIds = DomUtils::getDealerIds($data['domId']);
            $match = array_merge($match, ['DealerId' => ['$in' =>  array_map('strval', $domDealerIds)]]);
        }
        if (empty($data['from'])) {
            $match['Date'] = date('Y-m-d');
        } elseif (empty($data['to'])) {
            $match['Date'] = ['$gte' => $data['from']];
        } else {
            $match['Date'] = ['$gte' => $data['from'], '$lte' => $data['to']];
        }
        if (!empty($data['department'])) {
            if ($data['department'] == 'sales') {
                $match['IvrLog'][0][1] = $data['department'];
            } elseif ($data['department'] == 'service') {
                $match['IvrLog'][0][2] = $data['department'];
            } else {
                $match['IvrLog'][0][3] = $data['department'];
            }
        }
        $group = [
            '_id' => '$PandaCode',
            'dealer' => ['$first' => '$Dealer'],
            'location' => ['$first' => '$Location'],
            'misssed_count' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Missed Call']
                        ], 1, 0
                    ]
                ]
            ],
            'total_calls' => [
                '$sum' => 1
            ]
        ];
        $sort = [
            'misssed_count' => -1
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        );
        return $cursor['result'];
    }

    public static function getUniqueAndRepeatedCallerCount($data)
    {
        $match = [];
        if (isset($data['rsmId'])) {
            $rsmDealerIds = RsmUtils::getDealerIds($data['RsmId']);
            $match = array_merge($match, ['DealerId' => ['$in' => array_map('strval', $rsmDealerIds)]]);
        } elseif (isset($data['domId'])) {
            $domDealerIds = DomUtils::getDealerIds($data['domId']);
            $match = array_merge($match, ['DealerId' => ['$in' =>  array_map('strval', $domDealerIds)]]);
        } elseif (isset($data['dealerId'])) {
            $match['DealerId'] = (string) $data['dealerId'];
        }
        if (empty($data['from'])) {
            $match['Date'] = date('Y-m-d');
        } elseif (empty($data['to'])) {
            $match['Date'] = ['$gte' => $data['from']];
        } else {
            $match['Date'] = ['$gte' => $data['from'], '$lte' => $data['to']];
        }
        if (!empty($data['department'])) {
            if ($data['department'] == 'sales') {
                $match['IvrLog'][0][1] = $data['department'];
            } elseif ($data['department'] == 'service') {
                $match['IvrLog'][0][2] = $data['department'];
            } else {
                $match['IvrLog'][0][3] = $data['department'];
            }
        }
        $group = [
            '_id' => [
                'caller' => '$CallerId',
                'dealer' => '$DealerId'
            ],
            'call_count' => [
                '$sum' => 1
            ]
        ];
        $group2 = [
            '_id' => '$call_count',
            'processed_count' => [
                '$sum' => 1
            ]
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group],
            ['$group' => $group2]
        );
        $response = [
            'unique' => 0,
            'repeated' => 0
        ];
        foreach ($cursor['result'] as $result) {
            $response['unique'] += $result['processed_count'];
            if ($result['_id'] > 1) {
                $response['repeated'] += ($result['processed_count'] * $result['_id']) - $result['processed_count'];
            }
        }
        return $response;
    }

    public static function getMonthWiseStatus($data)
    {
        if (empty($data['from'])) {
            $match['Date'] = date('Y-m-d');
        } elseif (empty($data['to'])) {
            $match['Date'] = ['$gte' => $data['from']];
        } else {
            $match['Date'] = ['$gte' => $data['from'], '$lte' => $data['to']];
        }

        $group = [
            '_id' => '$Date',
            'connected' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Connected']
                        ], 1, 0
                    ]
                ]
            ],
            'missed' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Missed']
                        ], 1, 0
                    ]
                ]
            ],
            'ivrdrop' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'IVR Drop']
                        ], 1, 0
                    ]
                ]
            ],
            'offline' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Offline']
                        ], 1, 0
                    ]
                ]
            ],
            'total' => [
                '$sum' => 1
            ]
        ];
        $sort = [
            '_id' => 1
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        );

        return $cursor['result'];
    }

    public static function getStoreMissedCount($data)
    {
        if (empty($data['from'])) {
            $match['Date'] = date('Y-m-d');
        } elseif (empty($data['to'])) {
            $match['Date'] = ['$gte' => $data['from']];
        } else {
            $match['Date'] = ['$gte' => $data['from'], '$lte' => $data['to']];
        }

        $group = [
            '_id' => '$StoreId',
            'Location' => ['$first' => '$Location'],
            'StoreCode' => ['$first' => '$StoreCode'],
            'missed_count' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Missed']
                        ], 1, 0
                    ]
                ]
            ],
            'total_calls' => [
                '$sum' => 1
            ]
        ];
        $sort = [
            'missed' => -1
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate(
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        );
        return $cursor['result'];
    }
    public static function getConditions($params)
    {
        if (empty($params['date_from'])) {
            $params['date_from'] = date('Y-m-d', strtotime('first day of this month'));
        }
        if (empty($params['date_to'])) {
            $params['date_to'] = date('Y-m-d');
        }
        $conditions = [
            'Date' => [
                '$gte' => $params['date_from'],
                '$lte' => $params['date_to']
            ]
        ];
        if (!empty($params['StateId']) ) {
            $conditions = array_merge($conditions, array('StateId' => (int)$params['StateId']));
        }
        if (!empty($params['CityId'])) {
            $conditions = array_merge($conditions, array('CityId' => (int)$params['CityId']));
        }
        if (!empty($params['StoreId'])) {
            $conditions = array_merge($conditions, array('StoreId' => (int) $params['StoreId']));
        }
        if (!empty($params['CallerId'])) {
            $conditions = array_merge($conditions, array('CustomerNumber' => (int) $params['CallerId']));
        }
        if (!empty($params['Status'])) {
            $conditions = array_merge($conditions, array('Status' => (string) $params['Status']));
        }
        if (!empty($params['Type'])) {
            $conditions = array_merge($conditions, array('StoreType' => (string) $params['Type']));
        }
        if (!empty($params['Location'])) {
            $conditions = array_merge($conditions, array('Location' => (string) $params['Location']));
        }
        if (!empty($params['asmId'])) {
            $clusterIds = Asmutils::getClusterIdsByAsmId($params['asmId']);
            $conditions = array_merge($conditions, ['ClusterId' => ['$in' => array_map('intval',$clusterIds)]]);
        }
        if (!empty($params['rmId'])) {
            $regionIds = Rmutils::getRegionIdsByRmId($params['rmId']);
            $conditions = array_merge($conditions, ['RegionId' => ['$in' => array_map('intval',$regionIds)]]);
        }
        return $conditions;
    }

    public static function getStoreIds($params){
        $match = self::getConditions($params);
        $group = [
            '_id' => '$StoreId'
        ];
        $calllogCollection = new Calllog();
        $cursor = $calllogCollection->mongoStr->aggregate([
            ['$match' => $match],
            ['$group' => $group]
        ]);
        $stores = [];
        foreach ($cursor['result'] as $res) {
            $stores[] = $res['_id'];
        }
        return $stores;
    }
}
