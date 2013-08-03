<?php

/*
Plugin Name: Sync to Sendy
Plugin URI: www.romainsimon.net
Description: Synchronize Wordpress userbase with Sendy on registration using API
Version: 1.0
Author: Romain SIMON
Author URI: www.romainsimon.net
*/

/*  Copyright 2013  Romain SIMON  (email : contact [at] romainsimon [dot] net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/




class synctosendy {
   function  __construct() {
      // nothing
   }
}
 
if (class_exists('synctosendy')) {
   $synctosendy = new synctosendy();
}



add_action('init', 'syncinit');
function syncinit(){
	register_post_type('Sync to Sendy', 'd');	
}

add_action('admin_menu', 'fwds_plugin_settings');

function fwds_plugin_settings() {

    add_menu_page('Sync to Sendy', 'Sync to Sendy', 'administrator', 'syncsendy_settings', 'syncsendy_display_settings', plugins_url( 'synctosendy/images/sendy.png' ), 101);

}


function syncsendy_display_settings() {



    $sendyurl = (get_option('syncsendy_url') != '') ? get_option('syncsendy_url') : 'http://sendy.yourdomain.com/subscribe';
    $sendylist = (get_option('syncsendy_list') != '') ? get_option('syncsendy_list') : '1';


    $html = '</pre>
<div class="wrap"><form action="options.php" method="post" name="options">
<div class="top-pattern" style="background: url(\''.plugins_url( 'synctosendy/images/top-pattern.gif' ).'\') repeat-x 0 0;height: 8px;" ></div>
<h2>Sendy Settings</h2>

' . wp_nonce_field('update-options') . '
<table class="form-table" width="100%" cellpadding="10">
<tbody>
<tr>
<td colspan="2"><i>This plugin allows you to syncronize Wordpress userbase with your Sendy installation when someone register.<br />
Don\'t have Sendy yet? <a href="http://sendy.co/?ref=1JYIN">Download it here</a><br /></i>
</td>
</tr>
<tr>
<th scope="row">
	Sendy Installation subscribe URL
</th>
<td><input type="text" name="syncsendy_url" class="regular-text" value="' . $sendyurl . '" /></td>
</tr>
<tr>
<th scope="row">List ID</th>
<td><input type="text" name="syncsendy_list" class="small-text" value="' . $sendylist . '" /></td>
</tr>
</tbody>
</table>
 <input type="hidden" name="action" value="update" />

 <input type="hidden" name="page_options" value="syncsendy_url,syncsendy_list" />

 <input type="submit" name="Submit" class="button" value="Save settings" /></form></div>
<pre>
';

    echo $html;

}







add_action('user_register', 'subscribe_to_sendy');

function subscribe_to_sendy($user_id) {

    if ( isset( $_POST['first_name'] ) ) {
        update_user_meta($user_id, 'first_name', $_POST['first_name']);
    }


    $sendyurl = (get_option('syncsendy_url') != '') ? get_option('syncsendy_url') : 'http://sendy.yourdomain.com/';
    $sendylist = (get_option('syncsendy_list') != '') ? get_option('syncsendy_list') : '1';

	$params = array ('name' => $_POST['user_login'], 'email' => $_POST['user_email'], 'list' => $sendylist, 'boolean' => 'true');
    $query = http_build_query ($params);
      $contextData = array ( 
                    'method' => 'POST',
                    'header' => "Connection: close\r\n".
                                "Content-Length: ".strlen($query)."\r\n",
                    'content'=> $query );
      $context = stream_context_create (array ( 'http' => $contextData ));
      $result =  file_get_contents (
                  $sendyurl,
                  false,
                  $context);

    


}

?>