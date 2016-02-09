<?php

class FacturaConfig {

    static $apiUrl      = '';
    static $apiKey      = '';
	static $apiSecret   = '';
	static $serie       = '';
	static $dayOff      = '';
    static $title       = '';
    static $description = '';
    static $colorheader = '';
    static $colorfont   = '';

    /**
     * Saving configuration in .conf file
     *
     */
    static function saveConf($settings){
        $configFile = fopen(dirname(__FILE__) .'/facturacom.conf', 'w') or die('Unable to open file!');
        //write apiKey
        fwrite($configFile, ApiHelper::strEncode($settings['apikey'])."\n");
        //write apiSecret
        fwrite($configFile, ApiHelper::strEncode($settings['apisecret'])."\n");
        //write serie
        fwrite($configFile, ApiHelper::strEncode($settings['serie'])."\n");
        //write dayoff
        fwrite($configFile, ApiHelper::strEncode($settings['dayoff'])."\n");
        //write apiurl
        fwrite($configFile, ApiHelper::strEncode($settings['apiurl'])."\n");
        //write title
        fwrite($configFile, ApiHelper::strEncode($settings['title'])."\n");
        //write description
        fwrite($configFile, ApiHelper::strEncode($settings['description'])."\n");
        //write colorheader
        fwrite($configFile, ApiHelper::strEncode($settings['colorheader'])."\n");
        //write colorfont
        fwrite($configFile, ApiHelper::strEncode($settings['colorfont'])."\n");
        fclose($configFile);

        // @TODO validate the file has been written successfully
        return true;
    }

    /**
     * Getting local vars to use globally
     *
     * @return Array
     */
    static function configEntity(){
        $configVars = self::getConf();

        return array(
            'apikey'      => ApiHelper::strDecode($configVars[0]),
            'apisecret'   => ApiHelper::strDecode($configVars[1]),
            'serie'       => ApiHelper::strDecode($configVars[2]),
            'dayoff'      => ApiHelper::strDecode($configVars[3]),
            'apiurl'      => ApiHelper::strDecode($configVars[4]),
            'title'       => ApiHelper::strDecode($configVars[5]),
            'description' => ApiHelper::strDecode($configVars[6]),
            'colorheader' => ApiHelper::strDecode($configVars[7]),
            'colorfont' => ApiHelper::strDecode($configVars[8]),
        );
    }

    /**
     * Read configuration from .conf file
     *
     * @return Array
     */
     static function getConf(){
         $fp = @fopen(dirname(__FILE__) .'/facturacom.conf', 'r');

         //Add each line to an array
         if ($fp) {
            $configVars = explode("\n", fread($fp, filesize(dirname(__FILE__) .'/facturacom.conf')));
         }

         return $configVars;
     }

}
