<?php

namespace App\Http\Controllers;

use ErrorException;
use Exception;
use Illuminate\Http\Request;
use FastSimpleHTMLDom\Document as DOM;
use SoapClient;

use function GuzzleHttp\Promise\exception_for;

class DOMcontroller extends Controller
{

    /** Tracking Numbers */
    private static $track_numbers;

    /** TakeOut Prices in GEL */
    private static $takeouts;

    /** Get Transporting Price of Package if Tracking Exists */
    public static function priceByTracking($tracking,$item_state){

        try{
            $valute = new SoapClient('http://nbg.gov.ge/currency.wsdl');
            /** USD To GELL Currency */
            $valute = $valute->GetCurrency('USD');
        }catch(Exception $err){
            return response("SOAP Service Error",500);
        }

        /** 
         * SpeedUp Search if Package is NOT Finished Search NOT in takeout,paginout/2,ect... 
        */

        // if Item is NOT Finished - don`t search in Takeout - Paginout
        if($item_state == 0){
            if(!self::trackingPrice($tracking,'received') instanceof ErrorException) 
                return substr(self::trackingPrice($tracking,'received') / $valute,0,6);
            elseif(!self::trackingPrice($tracking,'pending') instanceof ErrorException) 
                return substr(self::trackingPrice($tracking,'pending') / $valute,0,6);
            elseif(!self::trackingPrice($tracking,'arrived') instanceof ErrorException) 
                return substr(self::trackingPrice($tracking,'arrived') / $valute,0,6);
            else return response("Item Not Found Or Not Authorised",500);
        }
        // if item is Finished - Search in Takeout - Paginout/2 - Paginout 3
        elseif($item_state == 1){
            if(!self::trackingPrice($tracking,'takeout') instanceof ErrorException)
                return substr(self::trackingPrice($tracking,'takeout') / $valute,0,6);
            elseif(!self::trackingPrice($tracking,'paginout/2') instanceof ErrorException)
                return substr(self::trackingPrice($tracking,'paginout/2') / $valute,0,6);
            elseif(!self::trackingPrice($tracking,'paginout/3') instanceof ErrorException)
                return substr(self::trackingPrice($tracking,'paginout/3') / $valute,0,6);
            else return response("Item Not Found Or Not Authorised",500);
        }
        else return responce("Bad State Identifier.",500);
    }

     /** Get Array Of Only Trackings */
    public static function onlyTrackNumbers(){
        try{
            return self::parseAllTracks(self::loadDOM('https://www.inex.ge/','pending'));
        }catch(Exception $error){
            return response("Tracking Number Not Found.",500);
        }
    }

     /** Get Array Of Trackings + Takeout */
     public static function trackNTakeout(){
        return self::parseTrackWithPricing(self::loadDOM('https://www.inex.ge/','takeout'));
    }

    /**
     *      M A J O R Privates
     * 
     */

    /** Get INEX.GE Transporting Price On Item */
    private static function trackingPrice($tracknum,$segment){    
        try{
            $array = self::parseTrackWithPricing(self::loadDOM('https://www.inex.ge/',$segment)); 

            foreach($array as $key => $val){

                if($key == $tracknum) {
                    
                    return $val;
                }
            }
            return new ErrorException();
        }catch(Exception $err){
            return responce("Server Not Found Or Busy.",404);
        }
    }
    /** Return Tracking Numbers */
    private static function parseAllTracks(DOM $dom){
        /** Find All <a class="tooltip">  */
        $tr_items = $dom->find('a[class=tooltip]'); 

        /** Tracking Numbers Array  */    
        self::$track_numbers = [];

        /** Removing Extra <SNAN> and <IMG> in each */
        foreach($tr_items as $row){
            $row = preg_replace("'<span>(.*?)</span>'si ", "", $row->innertext);
            $row = preg_replace("'<img(.*?)>'si ", "", $row);
            $row = substr($row,4);

            array_push(self::$track_numbers,$row);
        }
        return self::$track_numbers;
    }
    /** Return Tracking Numbers And Each Prices in GEL Assotiated Array */
    private static function parseTrackWithPricing(DOM $dom){

        /** Find All <a class="tooltip">  */
        $tr_items = $dom->find('a[class=tooltip]'); 

        /** Find All <span id="price">  */
        $tr_prices = $dom->find('span[id=price]');

        /** if We`re Checking TakeOut */
        $isTakeoutTab = false;

        if($tr_prices->innertext == ""){
            $tr_prices = $dom->find('td[style=\'color:#009900;\']');
            $isTakeoutTab = true;
        }

        /** Tracking Numbers Array  */    
        self::$track_numbers = [];

        /** Takeout Pricings Array */
        self::$takeouts = [];

        /** Removing Extra <SNAN> and <IMG> in each */
        foreach($tr_items as $row){
            $row = preg_replace("'<span>(.*?)</span>'si ", "", $row->innertext);
            $row = preg_replace("'<img(.*?)>'si ", "", $row);
            $row = substr($row,4);
            /** Fix Error Removing WhiteSpaces*/
            $row = str_replace(' ', '',$row);

            /** Fix issue Not Found By Removing \N and \T 10.29.19 */
            $row = str_replace("\n","",$row);
            $row = str_replace("\t","",$row);
            
            array_push(self::$track_numbers,$row);
        }

        /** Removing Extra <SNAN> and <IMG> in each */
        foreach($tr_prices as $single_price){
            //if($isTakeoutTab)
            //    array_push(self::$takeouts,floatval(substr($single_price->innertext,0,5)));
            //else 
            array_push(self::$takeouts,floatval($single_price->innertext));
        }

        $combined = array_combine(self::$track_numbers,self::$takeouts);

        return $combined;

    }

    /**
     *   Basic Private Functions
     * 
     */
    /** Load Authorised DOM With URL */
    private static function loadDOM($baseURL,$segment){
        try {

            /** Login Using Credentials from .ENV */
            self::doLogin($baseURL,env("INEX_CONFIG"));
            
            /** Grabb Authorised WebPage URL */
            $page = new DOM(self::grabPage($baseURL.$segment));
            return $page;
        }
        catch (exception $e) {
            return $e;
        }
    }

    /** Login Using Curl */
    private static function doLogin($url,$data){
        $fp = fopen("cookie.txt", "w");
        fclose($fp);
        $login = curl_init();
        curl_setopt($login, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($login, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt($login, CURLOPT_TIMEOUT, 40000);
        curl_setopt($login, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($login, CURLOPT_URL, $url);
        curl_setopt($login, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($login, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($login, CURLOPT_POST, TRUE);
        curl_setopt($login, CURLOPT_POSTFIELDS, $data);
        ob_start();
        return curl_exec ($login);
        ob_end_clean();
        curl_close ($login);
        unset($login);    
    } 

    /** Grab Page Using Curl */
    private static function grabPage($site){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt($ch, CURLOPT_URL, $site);
        ob_start();
        return curl_exec ($ch);
        ob_end_clean();
        curl_close ($ch);
    }
}
