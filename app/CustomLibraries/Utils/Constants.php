<?php

namespace App\CustomLibraries\Utils;

/**
 * Description of Constants
 *
 * @author waybeo
 */
class Constants {

    public static $ADMIN_DEFAULT_FIELDS_TO_DISPLAY = "CallStartTime,StoreCode,StoreType,Location,CustomerNumber,"
            . "WorkshopPandaCode,CallRecordUrl,Status,HangupLeg,CallType,"
            . "VirtualNumber,CallEndTime,City,State,Zone,StoreName,ConversationDuration,IVRDuration";

    public static $ADMIN_LISTING_FIELDS = [
        "CallStartTime",
        "ConversationDuration",
        "Location",
        "StoreType",
        "Status",
        "Rm",
        "Asm",
        "Region",
        "Cluster",
        "AgentNumber",
        "CallStartTime",
        "CallEndTime",
        "StoreCode",
        "CustomerNumber",
        "VirtualNumber",
        "CallRecordUrl",
        "IVRDuration",
        "BusyCalleesStr",
        "RingDuration",
        "City",
        "State",
        "Zone",
        "StoreName",
        "ConversationDuration",
        "IVRDuration",
        "HangupLeg",
        "CallType"
    ];

    public static $ADMIN_EXPORT_FIELDS = "CallStartTime,StoreCode,StoreType,Location,CustomerNumber,VirtualNumber,"
            . "CallRecordUrl,ConversationDuration,Status,BusyCalleesStr,ConnectedTo,HangupLeg,"
            . "Region,Cluster,Rm,Asm,CallEndTime,City,State,Zone,StoreName,ConversationDuration,IVRDuration,CallType";

    public static $CALLFLOW_JSON = '{
        "number": "",
        "app": "PureIt",
        "callflow": [
            {
                "WORKING_HOUR": {
                    "config": {
                        "all": [
                            {
                                "from": "0900",
                                "to": "1800"
                            }
                        ]
                    },
                    "true": [
                        {
                            "MENU": {
                                "prompts": {
                                    "menu": {
                                        "file": "pureit-welcome_menu"
                                    },
                                    "no_input": {
                                        "file": "no_input"
                                    },
                                    "invalid_input": {
                                        "file": "wrong_input"
                                    }
                                },
                                "repeat": 2,
                                "dtmf_logic": {
                                    "1": {
                                        "name": "sales",
                                        "logic": [
                                            {
                                                "PROMPT": {
                                                    "file": "pureit-will_recorded"
                                                }
                                            },
                                            {
                                                "CONNECT_GROUP": {
                                                    "statergy": "PRIORITY",
                                                    "gatewayId": 1,
                                                    "participants": []
                                                }
                                            }
                                        ]
                                    },
                                    "2": {
                                        "name": "service",
                                        "logic": [
                                            {
                                                "PROMPT": {
                                                    "file": "pureit-will_recorded"
                                                }
                                            },
                                            {
                                                "CONNECT_GROUP": {
                                                    "statergy": "PRIORITY",
                                                    "gatewayId": 1,
                                                    "participants": []
                                                }
                                            }
                                        ]
                                    }
                                },
                                "failure": [
                                    {
                                        "HANGUP": true
                                    }
                                ]
                            }
                        },
                        {
                            "PROMPT": {
                                "file": "no_answer"
                            }
                        },
                        {
                            "HANGUP": true
                        }
                    ],
                    "false": [
                        {
                            "HANGUP": "true"
                        }
                    ]
                }
            }
        ]
    }';
}
