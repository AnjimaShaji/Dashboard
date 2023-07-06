<?php

namespace App\CustomLibraries;

/**
 * 
 * Description of MongoPagination Handler Class
 * @brief       MongoPagination Handler Class
 * @description MongoPagination Handler Class
 * @author      Jaison John <jaison.john@waybeo.com>
 * @date        Wedenesday, 2017 July 19
 * @example     $pagination = new WVP_Common_Utils_MongoPagination($modelCallLog->mongoDb);
 *
 */

class MongoPagination
{

    /**
     * 
     * @brief  Constructor Method for MongoPagination Class
     * 
     * @description Constructor Method for MongoPagination Class
     * 
     * @author     Jaison John <jaison.john@waybeo.com>
     * @date       Wedenesday, 2017 July 19
     * @param type $mongoHandler
     * @param type $currentURL
     */
    public function __construct($mongoHandler, $currentURL = false)
    {
        $this->mongoHandler = $mongoHandler;
        $this->currentURL = $currentURL;
    }

    /**
     * 
     * @brief  setQuery Method for MongoPagination Class
     * 
     * @description setQuery Method for MongoPagination Class
     * 
     * @author     Jaison John <jaison.john@waybeo.com>
     * @date       Wedenesday, 2017 July 19
     * @param type $mongoHandler
     * @param type $currentURL
     */
    public function setQuery($queryParam, $currentPage = 1, $itemsPerPage = false)
    {
        $this->query = $queryParam;
        if (!empty($currentPage) && is_numeric($currentPage) && empty($itemsPerPage)) {
            $this->limitResult = $currentPage;
        } else {
            $this->currentPage = $currentPage;
            $this->itemsPerPage = $itemsPerPage;
        }

        return true;
    }
    
    /**
     * 
     * @brief  setQuery Method for MongoPagination Class
     * 
     * @description setQuery Method for MongoPagination Class
     * 
     * @author     Jaison John <jaison.john@waybeo.com>
     * @date       Wedenesday, 2017 July 19
     * @param type $mongoHandler
     * @param type $currentURL
     */
    public function paginate()
    {
        $collection = (!empty($this->query['#collection'])) ? $this->query['#collection'] : die('MongoPagination: no collection found');
        $find = (!empty($this->query['#find'])) ? $this->query['#find'] : array();
        $sort = (!empty($this->query['#sort'])) ? $this->query['#sort'] : array();
        $fields = (!empty($this->query['#fields'])) ? $this->query['#fields'] : array();

        //  Get total results count
        $this->totalItemCount = $this->mongoHandler->$collection->find($find)->count();

        /* 	Enable Limit based Query	 */
        if (!empty($this->limitResult)) {
            $resultSet = $this->mongoHandler->$collection->find($find)
                    ->sort($sort)
                    ->limit($this->limitResult);
            return array(
                'dataset' => iterator_to_array($resultSet),
                'totalItems' => $this->totalItemCount
            );
        } else {/* 	Enable Pagination based Query	 */ 
            $resultSet = $this->mongoHandler->$collection->find($find, $fields)
                    ->sort($sort)
                    ->limit($this->itemsPerPage)
                    ->skip($this->itemsPerPage * ($this->currentPage - 1));
            $this->totalPages = (floor($this->totalItemCount / $this->itemsPerPage)) + (($this->totalItemCount % $this->itemsPerPage) ? 1 : 0);

            return array(
                'dataset' => iterator_to_array($resultSet),
                'totalPages' => $this->totalPages,
                'totalItems' => $this->totalItemCount
            );
        }
    }

    /**
     * 
     * @brief  getPageLinks Method for MongoPagination Class
     * 
     * @description getPageLinks Method for MongoPagination Class
     * 
     * @author     Jaison John <jaison.john@waybeo.com>
     * @date       Wedenesday, 2017 July 19
     * @param Integer $setVisiblePagelinkCount
     * @param String $type
     */
    public function getPageLinks($setVisiblePagelinkCount = 9, $type = 'HTML')
    {
        $paginator['current'] = $this->currentPage;
        if (1 != $this->currentPage)
            $paginator['previous'] = 1;
        $paginator['pagesInRange'] = array();

        if ($this->currentPage + 4 > $this->totalPages) {
            $initial = $setVisiblePagelinkCount - (($this->totalPages - $this->currentPage) + 1);
            if ($this->currentPage - $initial > 0)
                $page = $this->currentPage - $initial;
            else
                $page = 1;
        }else {
            if ($this->currentPage - 4 > 0)
                $page = $this->currentPage - 4;
            else
                $page = 1;
        }
        $VisiblePagelinkCount = 1;
        for ($i = $page; $i <= $this->totalPages; $i++) {
            if ($VisiblePagelinkCount <= $setVisiblePagelinkCount) {
                $paginator['pagesInRange'][count($paginator['pagesInRange'])] = $i;
                $VisiblePagelinkCount++;
            } else
                break;
        }
        if ($this->totalPages != $this->currentPage)
            $paginator['next'] = 1;
        return $paginator;
    }

}