<?php

abstract class DbSettings{

    abstract  function bringMe();
    function getDynamicContents(){
        $this->bringMe();
    }

}



class SeoWrapper extends DbSettings{
    private $_errors = [];


    /**
     *  injecting custom made pages and their properties: title, keywords and description
     *
     * @param $customPages
     * @param $defaultSettings
     *
     */
    public function __construct($customPages, $defaultSettings){
        $this->customPages = $customPages;
        $this->defaultSettings = $defaultSettings;
    }


    /**
     * check if current page is defined inside static pages
     *
     * @param $currentUrl this is feched from the server variable REQUEST_URI
     * @return string this will either return a current page name or the word dynamic, if page ins't static
     *
     */

    public function isPageStaticOrDynamic($currentUrl){
        $pageName = array_keys($this->customPages['Pages']);
        $checkPage = (in_array($currentUrl, $pageName)) ? $this->customPages['Pages'][$currentUrl] : 'dynamic';
       return $checkPage;
   }


    /**
     * if page is dynamic, we need to fetch something to serve as title, key.. desc..
     *
     * @param $conn
     * @param $table specify the table from which we want to fetch datas
     * @param $identifier this checks if `something?=` is defined. could be id, q ...
     * @return string if query is success, we will return fetched results, else we will send message to error method
     */

    public function getDynamicContents($conn, $table, $identifier){
        if(!isset($_GET[$identifier]) || empty($_GET[$identifier])){
            return $this->_errors = 'Invalid URL is found';
        }else{

            function bringMe($value = []){
                return $value;
            }

            $rows = bringMe($value);
               var_dump($rows);
            try{
                $stmt = $conn->prepare("SELECT title, keywords, description FROM $table WHERE id = ? ");
                $stmt->execute([$_GET[$identifier]]);
            }catch(PDOException $e){
                return $this->_errors = 'Unknown error occured. Please try again'.$e->getMessage();
            }
            
            return ( $stmt->rowCount() == 0) ? $this->_errors = 'Page Not Found' : $stmt->fetchAll(PDO::FETCH_NUM)[0];
           
        }
    }


    /*
     * checks if error is found
     */
     public function hasErrors(){
         return (!empty($this->_errors)) ? true : false;
     }
 }


