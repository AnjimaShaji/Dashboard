<?php

return [
    
    // Service configurations.

    'services'          => [
        
        'soap'              => [
            'name'              => 'SoapAPI',
            'class'             => 'App\CustomLibraries\LeadService',
            'exceptions'        => [
                'Exception'
            ],
            'types'             => [
                'keyValue'          => 'Viewflex\Zoap\Demo\Types\KeyValue'
            ],
            'strategy'          => 'ArrayOfTypeComplex',
            'headers'           => [
                'Cache-Control'     => 'no-cache, no-store'
            ],
            'options'           => []
        ]
        
    ],

    
    // Log exception trace stack?

    'logging'       => true,

    
    // Mock credentials for demo.

    'mock'          => [
        'user'              => 'api-user@tatamotors.com',
        'password'          => '28eVXCwDVT8XLrEn',
        'token'             => 'y7TJHEJPoz5FsxsAKHrggT1PdmIjthWfRYzPSlTw24xQHR2cADtOUub1cZi2'
    ],

    
];
