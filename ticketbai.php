<?php
/*
 * Plugin Name: ticketbai
 * Plugin URI: https://github.com/goear/ticketbai
 * Description: Plugin de wordpress, software garante para emitir facturas con la normativa ticketbai
 * Author: goear
 * Author URI: https://github.com/goear/
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Repo: https://github.com/goear/ticketbai
*/

/*

We love open source projects. We love WP and many other collaborative projects.
Feel free to edit, add or remove whatever you want in ticketbai

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/


if ( ! defined( 'ABSPATH' ) ) exit;

if ( !function_exists("ticketbai_add_button_in_pages_and_posts") ) {
    function ticketbai_add_button_in_pages_and_posts($content) {

        $options = get_option( 'ticketbai_dot_li_content_settings' );

            if (!empty($_POST['cf-submitted']) && isset($_POST['nounce']) && wp_verify_nonce($_POST['nounce'], 'ticketbai_add_action'))
            {
                $name    = sanitize_text_field( $_POST["cf-name"] );
                $email   = sanitize_email( $_POST["cf-email"] );
                $subject = "Hey! Demo ticketbai";
                $date = 'Custom date: '.sanitize_text_field($_POST['custom_date']);
                $phone = 'Custom date: '.sanitize_text_field($_POST['phone']);
                $message = 'Date: '.$date.'<br>';
                $message .= 'Phone: '.$phone.'<br>';
                // get the blog administrator's email address
                $to = get_option( 'admin_email' );
                $headers = "From: $name <$email>" . "\r\n";
                if ( wp_mail( $to, $subject, $message, $headers ) ) {
                    echo '<h4 style="color: green;">Thank you! We will contact you as soon as we review your request.</h4>';
                } else {
                    echo 'An unexpected error occurred, check your email configuration';
                }
            }
            else {
                $calltoActionText = returnValidValueToticketbaiString('ticketbai_dot_li_call_to_action_content_value_field');
                add_thickbox();
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_style('jquery-ui-css');
                $contentAppend = '<script type="text/javascript">
                jQuery(document).ready(function($) {
                    $(\'.custom_date\').datepicker({
                    dateFormat : \'yy-mm-dd\'
                    });
                });
            </script><div id="my-ticketbai-layer" style="display:none;">
             <p>
             <h2>'.returnValidValueToticketbaiString('ticketbai_dot_li_call_to_action_content_value_field').'</h2>
                    <form action="'. esc_url( $_SERVER['REQUEST_URI'] ) .'" method="post">
                    <input type="hidden" name="nounce" value="'.wp_create_nonce('ticketbai_add_action').'">
                    <p>
                    '.returnValidValueToticketbaiString('ticketbai_dot_li_date_content_value_field').': <br/>
                    <input type="text" class="custom_date" name="start_date" value=""/> '.returnValidValueToticketbaiString('ticketbai_dot_li_time_content_value_field').': <input type="text" name="time"" value="" size="40" />
                    </p>
                    <p>
                    '.returnValidValueToticketbaiString('ticketbai_dot_li_name_content_value_field').' <br/>
                    <input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="" size="40" />
                    </p>
                    <p>
                    '.returnValidValueToticketbaiString('ticketbai_dot_li_email_content_value_field').' <br/>
                    <input type="email" name="cf-email" value="" size="40" />
                    </p>
                    <p>
                    '.returnValidValueToticketbaiString('ticketbai_dot_li_phone_content_value_field').'<br/>
                    <input type="text" name="cf-phone"  value="" size="40" />
                    </p>
                    <p><input type="submit" name="cf-submitted" value="'.returnValidValueToticketbaiString('ticketbai_dot_li_submit_content_value_field').'"></p>
                    </form>
             </p>
        </div>
            <a href="#TB_inline?&width=800&height=800&inlineId=my-ticketbai-layer" class="thickbox">'.returnValidValueToticketbaiString('ticketbai_dot_li_call_to_action_content_value_field').'</a>';
            return $contentAppend.$content;
            }
    }
    add_filter( 'the_content', 'ticketbai_add_button_in_pages_and_posts' );
}

add_action( 'admin_menu', 'ticketbai_dot_li_add_admin_menu' );
add_action( 'admin_init', 'ticketbai_dot_li_settings_init' );

function ticketbai_dot_li_add_admin_menu() {

    add_options_page( 'ticketbai', 'ticketbai configuration', 'manage_options', 'ticketbai_dot_li_configuration', 'ticketbai_dot_li_configuration_content_page' );

}

function ticketbai_dot_li_settings_init() {


    register_setting( 'ticketbaiPluginCustomContent', 'ticketbai_dot_li_content_settings' );

    add_settings_section(
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section',
        __( '', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        '',
        'ticketbaiPluginCustomContent'
    );

    add_settings_field(
        'ticketbai_dot_li_call_to_action_content_value_field',
        __( 'Call to action text:', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        'ticketbai_call_to_action_text_field_section_render',
        'ticketbaiPluginCustomContent',
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section'
    );


    add_settings_field(
        'ticketbai_dot_li_date_field_content_value_field',
        __( 'Date field:', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        'ticketbai_dot_li_date_field_section_render',
        'ticketbaiPluginCustomContent',
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'ticketbai_dot_li_time_field_content_value_field',
        __( 'Time field:', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        'ticketbai_dot_li_time_field_section_render',
        'ticketbaiPluginCustomContent',
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'ticketbai_dot_li_name_field_content_value_field',
        __( 'Name field:', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        'ticketbai_dot_li_name_field_section_render',
        'ticketbaiPluginCustomContent',
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'ticketbai_dot_li_email_field_content_value_field',
        __( 'Email field:', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        'ticketbai_dot_li_email_field_section_render',
        'ticketbaiPluginCustomContent',
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section'
    );


    add_settings_field(
        'ticketbai_dot_li_phone_field_content_value_field',
        __( 'Phone field:', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        'ticketbai_dot_li_phone_field_section_render',
        'ticketbaiPluginCustomContent',
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'ticketbai_dot_li_submit_field_content_value_field',
        __( 'Submit field:', 'ticketbai_dot_li_add_custom_content_to_bottom_of_post' ),
        'ticketbai_dot_li_submit_field_section_render',
        'ticketbaiPluginCustomContent',
        'ticketbai_dot_li_custom_content_to_bottom_of_post_section'
    );

}

function ticketbai_call_to_action_text_field_section_render() {

    $options = get_option( 'ticketbai_dot_li_content_settings' );
    ?>
    <input type='text' name='ticketbai_dot_li_content_settings[ticketbai_dot_li_call_to_action_content_value_field]' value='<?php

    echo returnValidValueToticketbaiString('ticketbai_dot_li_call_to_action_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}


function ticketbai_dot_li_name_field_section_render() {

    $options = get_option( 'ticketbai_dot_li_content_settings' );

    ?>
    <input type='text' name='ticketbai_dot_li_content_settings[ticketbai_dot_li_name_content_value_field]' value='<?php

    echo returnValidValueToticketbaiString('ticketbai_dot_li_name_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}


function ticketbai_dot_li_date_field_section_render() {

    $options = get_option( 'ticketbai_dot_li_content_settings' );
    ?>
    <input type='text' name='ticketbai_dot_li_content_settings[ticketbai_dot_li_date_content_value_field]' value='<?php

    echo returnValidValueToticketbaiString('ticketbai_dot_li_date_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}


function ticketbai_dot_li_time_field_section_render() {

    $options = get_option( 'ticketbai_dot_li_content_settings' );
    ?>
    <input type='text' name='ticketbai_dot_li_content_settings[ticketbai_dot_li_time_content_value_field]' value='<?php

    echo returnValidValueToticketbaiString('ticketbai_dot_li_time_content_value_field');
    ?>' cols='' style='width:100%' >
    <?php
}

function ticketbai_dot_li_email_field_section_render() {
    $options = get_option( 'ticketbai_dot_li_content_settings' );
    ?>
    <input type='text' name='ticketbai_dot_li_content_settings[ticketbai_dot_li_email_content_value_field]' value='<?php

    echo returnValidValueToticketbaiString('ticketbai_dot_li_email_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}

function ticketbai_dot_li_phone_field_section_render() {
    $options = get_option( 'ticketbai_dot_li_content_settings' );
    ?>
    <input type='text' name='ticketbai_dot_li_content_settings[ticketbai_dot_li_phone_content_value_field]' value='<?php

    echo returnValidValueToticketbaiString('ticketbai_dot_li_phone_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}

function ticketbai_dot_li_submit_field_section_render() {
    $options = get_option( 'ticketbai_dot_li_content_settings' );
    ?>
    <input type='text' name='ticketbai_dot_li_content_settings[ticketbai_dot_li_submit_content_value_field]' value='<?php

    echo returnValidValueToticketbaiString('ticketbai_dot_li_submit_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}

# Save
function ticketbai_dot_li_configuration_content_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>ticketbai in your language</h2>
        <?php
        settings_fields( 'ticketbaiPluginCustomContent' );
        do_settings_sections( 'ticketbaiPluginCustomContent' );
        submit_button();
        ?>
    </form>
    <?php
}

function returnValidValueToticketbaiString($key)
{
    $options = get_option( 'ticketbai_dot_li_content_settings' );
    if ( isset( $options[$key] ) && !empty( $options[$key] )) {
        return esc_html($options[$key]);
    }
    else {
        $defaultValues = [
            'ticketbai_dot_li_call_to_action_content_value_field' => 'Request a demo',
            'ticketbai_dot_li_date_content_value_field' => 'Date',
            'ticketbai_dot_li_time_content_value_field' => 'Time',
            'ticketbai_dot_li_name_content_value_field' => 'Your Name (required)',
            'ticketbai_dot_li_email_content_value_field' => 'Your Email (required)',
            'ticketbai_dot_li_phone_content_value_field' => 'Phone',
            'ticketbai_dot_li_submit_content_value_field' => 'Request a demo'
        ];
        return $defaultValues[$key];
    }
}

# Uninstall plugin
register_uninstall_hook( __FILE__, 'ticketbai_dot_li_plugin_uninstall' );
function ticketbai_dot_li_plugin_uninstall() {
    $options = get_option( 'ticketbai_dot_li_content_settings' );
    # Clear at uninstall
    $option_to_delete = 'ticketbai_dot_li_content_settings';
    delete_option( $option_to_delete );
}