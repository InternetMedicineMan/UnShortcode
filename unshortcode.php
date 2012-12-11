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

        $image_btn = BMLCommon::get_base_url() . "/images/form-button.png";
        $out = '<a href="#TB_inline?width=480&inlineId=select_usc_button" class="thickbox" id="add_uscbutton" title="Add UnShortcode Button"><img src="'.$image_btn.'" alt="Add UnShortcode Button" /></a>';
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
                    <div style="padding:15px 15px 0 15px;">
                        <h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;">Insert A Button</h3>
                        <span>
                            Choose your Button details below.
                        </span>
                    </div>
                    <div style="padding:15px 15px 0 15px;">
                        <select id="add_button_color">
                            <option value="">  Select a Color  </option>
                            <option value="blue">  Blue  </option>
                            <option value="gray">  Gray  </option>
                            <option value="red">  Red  </option>
                            <option value="green">  Green  </option>
                            <option value="smoke">  Smoke  </option>
                            <option value="black">  Black  </option>
                            <option value="purple">  Purple  </option>
                            <option value="orange">  Orange  </option>
                        </select> <br/>
                    </div>
                    <div style="padding:15px 15px 0 15px;">
                        <label for="button_text">Button Text</label> <input type="text" id="button_text" /><br />
                        <label for="button_link">Button Link</label> <input type="text" id="button_link" /><br />
                        <label for="button_title">Button Title</label> <input type="text" id="button_title" />
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