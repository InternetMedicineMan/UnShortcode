<?php
/*
Plugin Name: UnShortcode Plugin
Plugin URI: http://www.BlueMedicineLabs.com
Description: Easily create buttons without shortcode that you can see in the admin
Version: 0.6
Author: BlueMedicine Labs
Author URI: http://www.BlueMedicineLabs.com

------------------------------------------------------------------------
Copyright 2012-2013 BlueMedicineLabs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

if(!defined("BML_CURRENT_PAGE"))
    define("BML_CURRENT_PAGE", basename($_SERVER['PHP_SELF']));

if(!defined("IS_ADMIN"))
    define("IS_ADMIN",  is_admin());

require_once(WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)) . "/common.php");

add_action('init',  array('BMLUnshortcode', 'init'));

class BMLUnshortcode{
	
	//Plugin starting point. Will load appropriate files
    public static function init(){
	
		add_action( 'wp_enqueue_scripts', array('BMLUnshortcode','bml_add_usc_stylesheet') );
    
        //Adding "embed form" button
        add_action('media_buttons_context', array('BMLUnshortcode', 'add_form_button'));

        if(in_array(BML_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
            add_action('admin_footer',  array('BMLUnshortcode', 'add_mce_popup'));
        }

		add_filter( 'mce_css', array('BMLUnshortcode', 'bml_mce_css') );
		
	}

    // Enqueue plugin style-file
    public static function bml_add_usc_stylesheet() {
        // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'bml-style', plugins_url('css/style.css', __FILE__) );
        wp_enqueue_style( 'bml-style' );
    }
	
	//Adding styles to admin WYSIWYG Editor
	function bml_mce_css( $mce_css ) {
		if ( ! empty( $mce_css ) )
			$mce_css .= ',';
			
		$mce_css .= plugins_url( 'css/style.css', __FILE__ );
		return $mce_css;
	}
	
	//Action target that adds the "Insert Form" button to the post/page edit screen
    public static function add_form_button($context){
        $is_post_edit_page = in_array(BML_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'));
        if(!$is_post_edit_page)
            return $context;

        $image_btn = BMLCommon::get_base_url() . "/images/form-button.gif";
        $out = '<a href="#TB_inline?width=480&inlineId=select_usc_button" class="thickbox button" id="add_uscbutton" title="Add UnShortcode Button" style="padding-left: .4em;"><img src="'.$image_btn.'" alt="Add UnShortcode Button" /> UnShortcode</a>';
        return $context . $out;
    }
    
	
	//Action target that displays the popup to insert a form to a post/page
    public static function add_mce_popup(){
        ?>
        <script>
            function InsertButton(){
                var button_color = jQuery("#add_button_color").val();
                if(button_color == ""){
                    alert("Please select a color");
                    return;
                }

                var button_text = jQuery("#button_text").val();
                var button_link = jQuery("#button_link").val();
                var button_title = jQuery("#button_title").val();

                window.send_to_editor("<a href=\"" + button_link + "\" class=\"uscbutton " + button_color + "\" title=\"" + button_title + "\">" + button_text + "</a>");
            }
        </script>
	
        <div id="select_usc_button" style="display:none;">
            <div class="wrap">
                <div>
                    <div style="padding:0;">
                        <p class="howto" style="margin: 3px;">Choose your Button details below.</p>
                    </div>
                    <div style="padding:0 15px 0 15px;">
                        <label for="add_button_color"><span style="display:inline-block;width: 80px;text-align: right;padding-right: 5px;">Color</span><select id="add_button_color">
                            <option value="">  Select a Color  </option>
                            <option value="blue">  Blue  </option>
                            <option value="gray">  Gray  </option>
                            <option value="red">  Red  </option>
                            <option value="green">  Green  </option>
                            <option value="smoke">  Smoke  </option>
                            <option value="black">  Black  </option>
                            <option value="purple">  Purple  </option>
                            <option value="orange">  Orange  </option>
                        </select></label> <br/>
                    </div>
                    <div style="padding:0 15px 0 15px;">
                        <label for="button_text"><span style="display:inline-block;width: 80px;text-align: right;padding-right: 5px;">Text</span><input type="text" id="button_text" style="width: 360px;margin-top: 5px;" /></label><br />
                        <label for="button_link"><span style="display:inline-block;width: 80px;text-align: right;padding-right: 5px;">URL</span><input type="text" id="button_link" value="http://" style="width: 360px;margin-top: 5px;" /></label><br />
                        <label for="button_title"><span style="display:inline-block;width: 80px;text-align: right;padding-right: 5px;">Title</span><input type="text" id="button_title" style="width: 360px;margin-top: 5px;" /></label>
                    </div>
                    <div style="padding:15px;">
                        <input type="button" class="button-primary" value="Insert Button" onclick="InsertButton();"/>&nbsp;&nbsp;&nbsp;
                    <a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
    
}