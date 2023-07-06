<?php

namespace App\CustomLibraries;
use Illuminate\Support\Facades\Config;
use MongoClient;

/**
 * Description of Calllog Connector Class
 * @brief Mongo class for connnecting to calllog collection 
 * @description Mongo class for connnecting to calllog collection 
 * @author Jaison John <jaison.john@waybeo.com>
 * @date Wedenesday, 2017 July 19
 * @example $calllog = new Calllog();
 *          $cursor = $calllog->mongoStr->find();
 */
class Calllog {
    
    public $mongoStr;
    public $mongoDb;
    
    public function __construct()
    {
        $db = Config::get('database.connections.mongodb');
        $mongoConInstance = new MongoClient ("mongodb://".$db['username'].":".$db['password']."@".$db['host'].":".$db['port']."/".$db['database']);
        $this->mongoDb = $mongoConInstance->selectDB($db['database']);
        $this->mongoStr = $this->mongoDb->selectCollection("calllogs");
    }

}
