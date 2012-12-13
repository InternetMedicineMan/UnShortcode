<?php
class BMLCommon{
	
	public static $version = "0.8";

	//Returns the url of the plugin's root folder
    public static function get_base_url(){
        $folder = basename(dirname(__FILE__));
        return plugins_url($folder);
    }
    
    //Returns the physical path of the plugin's root folder
    public static function get_base_path(){
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . "/" . $folder;
    }

}