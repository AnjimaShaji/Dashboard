<?php

namespace App\CustomLibraries;
use App\CustomLibraries\Utils\RccmServiceUtils;
use App\CustomLibraries\Utils\RmSalesUtils;

/**
 * Description of DataLayerCallLogUtils Class
 * @brief DataLayerCallLogUtils class for getting custom calllog collection
 * @description Mongo class for connnecting to calllog collection 
 * @example DataLayerCallLogUtils::getCalllog();
 */

class DataLayerCallLogUtils
{
    public static function getCalllogs($params, $itemsPerPage = 1, $currentPage = 1)
    {
        $conditions = self::getConditions($params);
        \Log::info(json_encode($conditions));
        $response = DatalayerUtils::paginate($currentPage, $itemsPerPage, $conditions);
        return $response;
    }
    public static function getCounts($params)
    {
        $count = [
            'Connected' => 0,
            'Missed' => 0,
            'IVR Drop' => 0,
            'total' => 0,
            'conversationDuration' => 0,
            'PRINT' => 0,
            'DIGITAL' => 0,
            'HYPERLOCAL' => 0
        ];
        $conditions = self::getConditions($params);
        $group1 = [
            '_id' => '$Status',
            'count' => ['$sum' => 1]
        ];
        $group2 = [
            '_id' => '$id',
            'total' => ['$sum' => 1],
            'conversationDuration' => ['$sum' => '$ConversationDuration']
        ];
        $group3 = [
            '_id' => '$VNType',
            'count' => ['$sum' => 1]
        ];
        $project = [
            '_id' => 0,
            'total' => '$total',
            'conversationDuration' => '$conversationDuration'
        ];
        $cursor1 = DatalayerUtils::aggregate([
            ['$match' => $conditions],
            ['$group' => $group1]
        ]);
        $cursor2 = DatalayerUtils::aggregate([
            ['$match' => $conditions],
            ['$group' => $group2],
            ['$project' => $project]
        ]);
        $cursor3 = DatalayerUtils::aggregate([
            ['$match' => $conditions],
            ['$group' => $group3]
        ]);
        foreach ($cursor1['result'] as $res) {
            $count[$res['_id']] = $res['count'];
        }
        foreach ($cursor3['result'] as $res) {
            $count[$res['_id']] = $res['count'];
        }
        if(!empty($cursor2['result'][0]))
        $count = array_merge($count,$cursor2['result'][0]); 
        return $count;
    }
    public static function getConditions($params)
    {
        $conditions = [
            'CallDate' => [
                '$gte' => $params['date_from'],
                '$lte' => $params['date_to']
            ]
        ];
        if (!empty($params['dealer'])) {
            $conditions = array_merge($conditions, array('DealerId' => (int) $params['dealer']));
        }
        if (!empty($params['Status']) && isset($params['Status'])) {
            $conditions = array_merge($conditions, array('Status' => (string) $params['Status']));
        }
        if (!empty($params['Zone'])) {
            $conditions = array_merge($conditions, array('ZoneId' => (int) $params['Zone']));
        }
        if (!empty($params['Region'])) {
            $conditions = array_merge($conditions,array('RegionId' => (int) $params['Region']));
        }
        if (!empty($params['State'])) {
            $conditions = array_merge($conditions, array('DivisionStateId' => (int) $params['State']));
        }
        if (!empty($params['City'])) {
            $conditions = array_merge($conditions,  array('DivisionCityId' => (int) $params['City']));
                
        }
        if (!empty($params['Division'])) {
            $conditions = array_merge($conditions, array('DealerDivisionId' => (int) $params['Division']));
        }
        if (!empty($params['vnType'])) {
            if(is_array($params['vnType'])){
                $conditions = array_merge($conditions, ['VNType' => ['$in' => array_map('strval',$params['vnType'])]]);
            }else{
                $conditions = array_merge($conditions, array('VNType' => (string) $params['vnType']));
            }
        }
        if (!empty($params['callBack'])) {
            if(is_array($params['callBack'])){
                $conditions = array_merge($conditions, ['CallBackStatus' => $params['callBack']]);
            }else{
                $conditions = array_merge($conditions, array('CallBackStatus' => (string) $params['callBack']));
            }
        }
        if (!empty($params['callType'])) {
            if(is_array($params['callType'])){
                $conditions = array_merge($conditions, ['CallType' => ['$in' => array_map('strval',$params['callType'])]]);
            }else{
                $conditions = array_merge($conditions, array('CallType' => (string) $params['callType']));
            }
        }
        if(!empty($params['rccmServiceId'])){
            $regionIds = RccmServiceUtils::getRegionIdsByRccmServiceId($params['rccmServiceId']);
            $conditions = array_merge($conditions,[
                '$or' => [
                    ['RegionId' => ['$in' => array_map('strval', $regionIds)]],
                    ['RegionId' => ['$in' => array_map('intval', $regionIds)]]
                ]
            ]);
            
        }
        if(!empty($params['rmSalesId'])){
            $regionIds = RmSalesUtils::getRegionIdsByRmSalesId($params['rmSalesId']);
            $conditions = array_merge($conditions,[
                '$or' => [
                    ['RegionId' => ['$in' => array_map('strval', $regionIds)]],
                    ['RegionId' => ['$in' => array_map('intval', $regionIds)]]
                ]
            ]);
           
        }
        if (!empty($params['range'])) {
            $conditions = array_merge($conditions, array('Range' => (string) $params['range']));
        }
        return $conditions;
    }

    public static function fetchCallsStatusByDay($params)
    {
        $match = self::getConditions($params);
        $group = [
            '_id' => [
                'Month' => '$CallDate'
            ],
            'Connected Call' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$Status', 'Connected']
                        ], 1, 0
                    ]
                ]
            ],
            'Missed Call' => [
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
            'Total Calls' => [
                '$sum' => 1
            ]
        ];
        $sort = [
            '_id' => 1
        ];
       
        $cursor = DatalayerUtils::aggregate([
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        ]);
        return $cursor['result'];
    }

    public static function getOpenClosedMissedCalls($params)
    {
        $match = self::getConditions($params);
        $group = [
            '_id' => null,
            'agent_closed' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$CallBackStatus', 'AgentCallback']
                        ], 1, 0
                    ]
                ]
            ],
            'customer_closed' => [
                '$sum' => [
                    '$cond' => [
                        [
                            '$eq' => ['$CallBackStatus', 'CustomerCallback']
                        ], 1, 0
                    ]
                ]
            ],
            'total_missed' => [
                '$sum' => 1
            ]
        ];
        $sort = [
            '_id' => 1
        ];
       
        $cursor = DatalayerUtils::aggregate([
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        ]);
        return $cursor['result'];
    }

    public static function getDailyReportAggregate($params)
    {
        $match = self::getConditions($params);
        $group = [
            '_id' => ['Type' => '$VNType', 'Status' => '$Status', 'CallType' => '$CallType'],
            'sum' => ['$sum' => 1]
        ];
        $sort = [
            '_id' => 1
        ];
        $cursor = DatalayerUtils::aggregate([
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort]
        ]);
        return $cursor['result'];
    }

    public static function mapFilterFields($fields)
    {
        $fields = str_replace('CustomerMobile', 'CustomerNumber', $fields);
        $fields = str_replace('VirtualNumberType', 'VNType', $fields);
        $fields = str_replace('CallStatus', 'Status', $fields);
        return $fields;
    }
    public static function getVirtualNumbers($params){
        $match = self::getConditions($params);
        $group = [
            '_id' => '$VirtualNumber'
        ];
        $cursor = DatalayerUtils::aggregate([
            ['$match' => $match],
            ['$group' => $group]
        ]);
        $numbers = [];
        foreach ($cursor['result'] as $res) {
            $numbers[] = $res['_id'];
        }
        return $numbers;
    }
}
