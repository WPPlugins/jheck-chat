<?php
/**
 * Functions
 * @since 1.0
 * @package Jheck Chat
 */
/**
 * Retrieve option value
 * @since 1.0
 */
if (!function_exists('jc_option_val')) {    
    function jc_option_val( $name )
    {
        return vp_option( "jheck_option." . $name );
    }
}

/**
 * Encrypting of files
 * @since 1.4
 */

if (!function_exists('jc_encrypt')) {
    
    function jc_encrypt( $q ) {
        $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( JC_ENCRYPTION_KEY ), $q, MCRYPT_MODE_CBC, md5( md5( JC_ENCRYPTION_KEY ) ) ) );
        return( $qEncoded );
    }
}

/**
 * Decrypting of encrypted files
 * @since 1.4
 */

if (!function_exists('jc_decrypt')) {
    function jc_decrypt( $q ) {
        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( JC_ENCRYPTION_KEY ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( JC_ENCRYPTION_KEY ) ) ), "\0");
        return( $qDecoded );
    }
}


/**
 * Ajax call validation
 * @since 1.3
 */
if (!function_exists('jc_is_ajax')) {
    function jc_is_ajax(){

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            return true; 
        }

        return false;
    }
}

/**
 * Invalid request, JSON output
 * @since 1.3
 */

if (!function_exists('jc_invalid_request')) {

    function jc_invalid_request(){
        $request = array();
        $request['request'] = 'Invalid';
        $return = header('Content-Type: application/json');
        $return .= die(json_encode( $request ));

        echo $return;
    }
}


/**
 * Create mysql database
 * @since 1.0
 */
if (!function_exists('jc_create_inbox')) {
    function jc_create_inbox() {

        global $wpdb;
        global $jc_db_version;
        $jc_installed_ver = get_option("jc_db_version");

        if ( $jc_installed_ver != $jc_db_version ){

            /**
             * If new version found, upgrade database
             * @since 1.1
             */
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = JC_MYSQL_INBOX;

            if ($jc_installed_ver < $jc_db_version) {
                
                /**
                 * Drop trash table column if exists
                 * DB version 1.1 below
                 * @since 1.4
                 */
                if ($wpdb->query("SHOW COLUMNS FROM $table_name LIKE 'trash'")) {
                    $wpdb->query("ALTER TABLE $table_name DROP COLUMN trash");
                };
                
            }

            $sql = "CREATE TABLE $table_name (
                id bigint(9) NOT NULL AUTO_INCREMENT,
                name varchar(300) NOT NULL,
                user_id bigint(9) NOT NULL,
                message varchar(300) NOT NULL,
                chat_date varchar(300) NOT NULL,
                ip varchar(300) NOT NULL,        
                unique_name varchar(300) NOT NULL,                
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            dbDelta( $sql );
            add_option( 'jc_db_version', $jc_db_version );

        }    
    }
}

/**
 * Install Scripts
 * @since 1.0
 */

if (!function_exists('jc_enqueue_scripts')) {
    function jc_enqueue_scripts() {
        /**
         * Include styles
         * @since 1.0
         */
        wp_enqueue_style( 'jheck_chat-style', JC_URL .'sources/css/style.css');
        wp_enqueue_style( 'jheck_chat-font-awesome', JC_URL .'sources/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style( 'jheck_chat-tempalte-style', JC_URL .'template/'.JC_TEMPLATE_NAME.'/jc_template-style.css');
        wp_enqueue_style( 'jheck_chat-custom', JC_URL .'sources/css/custom-style.css');

        
        /**
         * Include scripts
         * @since 1.0
         */    
        wp_enqueue_script( 'jheck_chat-script', JC_URL .'sources/js/custom-scripts.js', array(), '20141105', true );
        // wp_enqueue_script( 'jheck_chat-script', JC_URL .'sources/js/custom-scripts.min.js', array(), '20141105', true );
        wp_enqueue_script( 'jheck_chat-template-script', JC_URL .'template/'.JC_TEMPLATE_NAME.'/jc_template-script.js', array(), '20141105', true );
    }
}


/**
 * Insert messages
 * @since 1.0
 */

if (!function_exists('jc_insert_message')) {
    function jc_insert_message($user_name,$jc_user_id,$message,$chat_date,$ip,$jc_unique_name){

        global $wpdb;
        $table_name = JC_MYSQL_INBOX; 
        /**
         * Decyrpt all encrypted files
         * @since 1.4
         */  

        $user_name = $user_name;
        $jc_user_id = jc_decrypt($jc_user_id);

        $table_content = array( 
            'name' => $user_name,
            'user_id'   => $jc_user_id,
            'message' => $message,
            'chat_date' => $chat_date,
            'ip' => $ip,
            'unique_name' => $jc_unique_name
            );
        $data_format = array( '%s','%d','%s','%s','%s','%s');
        $insert_data = $wpdb->insert( $table_name,$table_content,$data_format);

        return $insert_data;
    }
}


/**
 * Get user ip
 * @since 1.0
 */
if (!function_exists('jc_get_ip')) {

    function jc_get_ip(){
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }

        if ($ip == '::1') {
            $ip = '127.0.0.1';
        }

        return $ip;
    }
}


/**
 * Get date depending on timezone
 * @since 1.0
 */
if (!function_exists('jc_get_date')) {
    function jc_get_date(){ 
        return date("Y-m-d H:i:s");
    }
}


/**
 * Display time ago.
 * @since 1.0
 */

if (!function_exists('jc_time_elapsed_string')) {
    function jc_time_elapsed_string($datetime, $full = false) {   

        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}


/**
 * Generate custom css.
 * @since 1.0
 */
if (!function_exists('jc_generate_custom_css')) {
    function jc_generate_custom_css($newdata) {

        $data = $newdata;   
        $css_dir = JC_URL_PATH . 'sources/css/'; // Shorten code, save 1 call
        ob_start(); // Capture all output (output buffering)

        require(JC_URL_PATH . 'inc/custom-css.php'); // Generate CSS

        $css = ob_get_clean(); // Get generated CSS (output buffering)
        file_put_contents($css_dir . 'custom-style.css', $css, LOCK_EX); // Save it
    }
}


/**
 * Sanitize content messages for malicious scripts.
 * @since 1.0
 */
if (!function_exists('jc_sanitize_content')) {
    function jc_sanitize_content($input) { 

        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $input);
    }
}


/**
 * Template creation
 * @since 1.2
 */
if (!function_exists('jc_chat_box_template')) {
    function jc_chat_box_template()
    {    
        $i = 0;
        $jc_template_dir = JC_URL_PATH.'template/';
        $jc_scanned_template = scandir($jc_template_dir);
        $result = array();

        foreach ($jc_scanned_template as $jc_template)
        {
            if ($i >= 2) {
                $result[] = array(
                    'value' => strtolower($jc_template), 
                    'label' => strtoupper($jc_template), 
                    'img' => JC_URL.'template/'.$jc_template.'/preview.png',
                );
            }
            $i++;
        }
        return $result;
    }

}
VP_Security::instance()->whitelist_function('jc_chat_box_template');

/**
 * Log if plugin is activated
 * @since 1.3
 */
if (!function_exists('jc_plugin_activated')) {

    function jc_plugin_activated(){
   
        $post_data['url'] = home_url();
        $post_data['action'] = 'Activated';
        $post_data['ip'] = $_SERVER['REMOTE_ADDR'];
         
        //traverse array and prepare data for posting (key1=value1)
        foreach ( $post_data as $key => $value) {
            $post_items[] = $key . '=' . $value;
        }
         
        //create the final string to be posted using implode()
        $post_string = implode ('&', $post_items);
        
        $curl_connection = 
          curl_init('http://jheck-chat.esy.es/?plugin_activated');
        
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
        
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
        
        $result = curl_exec($curl_connection);
         
        // print_r(curl_getinfo($curl_connection));
        curl_errno($curl_connection) . '-' . 
        curl_error($curl_connection);
        curl_close($curl_connection);

    }
}

/**
 * Log if plugin is deactivated
 * @since 1.3
 */
if (!function_exists('jc_plugin_deactivated')) {
    
    function jc_plugin_deactivated(){
   
        $post_data['url'] = home_url();
        $post_data['action'] = '!-- Deactivated';
        $post_data['ip'] = $_SERVER['REMOTE_ADDR'];
         
        //traverse array and prepare data for posting (key1=value1)
        foreach ( $post_data as $key => $value) {
            $post_items[] = $key . '=' . $value;
        }
         
        //create the final string to be posted using implode()
        $post_string = implode ('&', $post_items);
        
        $curl_connection = 
          curl_init('http://jheck-chat.esy.es/?plugin_activated');
        
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
        
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
        
        $result = curl_exec($curl_connection);
         
        // print_r(curl_getinfo($curl_connection));
        curl_errno($curl_connection) . '-' . 
        curl_error($curl_connection);
        curl_close($curl_connection);

    }
}

/**
 * Check user role
 * @since 1.4
 */

function jc_get_role($id){
    global $wpdb;

    $wp_usermeta = $wpdb->prefix.'usermeta';

    $role = explode('"',$wpdb->get_var("SELECT meta_value FROM $wp_usermeta WHERE meta_key = 'wp_capabilities' and user_id = '$id'"));
    return $role[1];
}

/**
 * New custom template creation support
 * @since 1.2
 */
if (!function_exists('jc_footer_chat_box_code')) {

    function jc_footer_chat_box_code(){    

        global $wpdb;    
        global $current_user;
        get_currentuserinfo();

        $table_name = JC_MYSQL_INBOX;
        $jc_hide_field = '';
        $jc_disable_field ='';

        include( JC_URL_PATH.'inc/class/template.class.php' );  

        /**
         * Logged in user details
         * @since 1.4
         */
        $jc_user_id = 0;
        $jc_user_role = '';

        if (is_user_logged_in()) {
            global $current_user;
            get_currentuserinfo();
            $username = $current_user->display_name;
            $jc_user_id = $current_user->ID;
            $jc_user_role = jc_get_role($jc_user_id);
        }


        /**
         * Will display total number of messages on each display
         * @since 1.3
         */
        
        $max_chat = 25;

        if (jc_option_val('jheck_chat_max_chatbox')) {
            $max_chat = jc_option_val('jheck_chat_max_chatbox');
        }

        $total_messages = count($wpdb->get_results( "SELECT * FROM $table_name"));

            if ($total_messages <= $max_chat) {     
                $limit = '';
            }else{
                $start_message = $total_messages - $max_chat;   
                $limit = 'LIMIT '.$start_message.','.$max_chat;
            }

        $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id ASC $limit");

        $jc_message_details = array();

            foreach ($results as $result){

                $chat_date = $result->chat_date;
                $chat_message = htmlentities($result->message);
                $chat_message = str_replace('\r\n', '<br>', $chat_message);
                $chat_message = str_replace('\r', '<br>', $chat_message);

                /**
                 * If user is unregistered, add unique name on display.
                 * @since 1.2
                 */

                if (!empty($result->unique_name)) {
                    $chat_name = $result->name .' <span class="jc_unique_name">('.$result->unique_name.')</span>';
                }else{
                    /**
                     * If user is logged in, display "You" instead of name
                     * @since 1.3
                     */
                    $username = '';

                    if (is_user_logged_in()) {
                        $username = $current_user->display_name;
                    }

                    if (!empty($username) && $result->name == $username) {
                        $chat_name = 'You';
                    }else{
                        $chat_name = $result->name;                    
                    }
                }        

                /**
                 * Put it inside an array
                 * @since 1.2
                 */

                $profile_user_image_url = JC_URL.'sources/img/default-profile-pic.png';

                $jc_message_details[] = array(
                    'jheck_chat_id' => $result->id,
                    'jheck_chat_name' => $chat_name,
                    'jheck_chat_message' => $chat_message,
                    'jheck_chat_date' => jc_time_elapsed_string($chat_date),                    
                    'jheck_chat_profile_image_url' => $profile_user_image_url,
                    'jheck_chat_profile_image' => '<img src="'.$profile_user_image_url.'" class="jheck_chat_profile_pic" alt="Chat user image">',
                );

            } 

        /**
         * Loop through the messages and creates a template for each one.
         * Because each message is an array with key/value pairs defined, 
         * we made our template so each key matches a tag in the template,
         * allowing us to directly replace the values in the array.
         * We save each template in the $jc_messages_template array.
         */
        $jc_messages_template = array();

        foreach ($jc_message_details as $jc_message) {

            $row = new Template(JC_URL_PATH.'template/'.JC_TEMPLATE_NAME.'/jc_list_messages.tpl');
            
            foreach ($jc_message as $key => $value) {
                $row->set($key, $value);
            }
            $jc_messages_template[] = $row;
        }    

        /**
         * If Jheck chat is disabled, hide messages
         * @since 1.2
         */
        $jheck_chat_content='';
        if (jc_option_val( 'jheck_chat_activate_chat' )) {
            $jheck_chat_content = Template::merge($jc_messages_template);
        }

        /**
         * Create form field for template
         * @since 1.2
         */

        /**
         * Message counter
         */

        $jc_chat_form = '';
        $jc_chat_form .= '<div id="jheck_chat-old-new-counter-wrapper" style="display:none;"><span class="jheck_chat_old_count">'.$total_messages.'</span></div>';


        /**
         * If chat is disable, hide form
         * @since 1.0
         */

        if (!jc_option_val( 'jheck_chat_activate_chat' )) {
            
            $jc_chat_form .= 'Jheck Chat disabled';

            if (is_user_logged_in()) {
                if ($jc_user_role == 'administrator') {
                    $jc_chat_form .= ' <a href="'.get_admin_url().'admin.php?page=jheck_option#_jheck_chat_display_settings">activate here.</a>';
                }    
            }

        }else{

            /**
             * If chat is enabled display form
             */

            
            /**
             * Login form args
             * @since 1.4
             */
            $jc_login_form_args = array(
                'echo'           => false,
                'remember'       => false,
                'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                'form_id'        => 'jheck_chat_loginform',
                'id_username'    => 'user_login',
                'id_password'    => 'user_pass',
                'id_remember'    => 'rememberme',
                'id_submit'      => 'jc_login_submit',
                'label_username' => __( 'Username' ),
                'label_password' => __( 'Password' ),
                'label_remember' => __( 'Remember Me' ),
                'label_log_in'   => __( 'Log In' ),
                'value_username' => '',
                'value_remember' => false
            );

            /**
             * Registered users only can use chat
             * @since 1.0
             */

            if (jc_option_val( 'jheck_chat_user_login' ) && !is_user_logged_in()) {

                $jc_chat_form .= '<p><a href="#" id="jheck_chat_login_button">Login</a> to join chat';
                    
                    /**
                     * Display registration link
                     * @since 1.4
                     */
                    if ( get_option( 'users_can_register' ) ) {
                        $jc_chat_form .= ' or <a href="'.wp_registration_url().'">register</a>';
                    }

                $jc_chat_form .= '.</p>';
            }
            else{


                $jc_chat_form .= '<form id="jheck_chat_form">';

                /**
                 * If user is not logged in, display name field.
                 * @since 1.0
                 */
                 
                if (!is_user_logged_in()) { 

                    $jc_chat_form .= '<p id="jheck_chat_new_message_alert"><a href="#" id="jheck_chat_new_message_alert_view">View new message*</a></p>';           

                    $jc_chat_form .= '<p>Enter your name or <a href="#" id="jheck_chat_login_button">Login</a> ';
                    
                        /**
                         * Display registration link
                         * @since 1.4
                         */
                        if ( get_option( 'users_can_register' ) ) {
                            $jc_chat_form .= ' or <a href="'.wp_registration_url().'">register</a>';
                        }

                    $jc_chat_form .= ' to join chat.</p>';
                    $jc_chat_form .= '<input type="text" id="jheck_chat_user" name="jheck_chat_user" placeholder="Enter your desired name" required autocomplete="off" minlength="3" maxlength="20">';
                    $jc_chat_form .= '<input type="hidden" id="jheck_chat_user_id" name="jheck_chat_user_id" value="'.jc_encrypt('0').'">';
                    $jc_chat_form .= '<input type="hidden" id="hidden_jheck_chat_user" name="jheck_chat_user_unique" readonly value='.uniqid().'>';
                    $jc_hide_field = 'style="display:none"';
                    $jc_disable_field ='disabled="disabled"';
                }
                else{

                    /**
                     * If user is already loggedin
                     * @since 1.3
                     */

                    $jc_chat_form .= '<input type="hidden" id="jheck_chat_user" name="jheck_chat_user_encrypted" value="'.jc_encrypt($username).'">';
                    $jc_chat_form .= '<input type="hidden" id="jheck_chat_user_id" name="jheck_chat_user_id" value="'.jc_encrypt($jc_user_id).'">';
                    $jc_hide_field = '';
                }

            /**
             * Message box field
             */

            $jc_chat_form .= '<textarea name="jheck_chat_message" id="jheck_chat_message" placeholder="Write your message here." required '.$jc_hide_field.' '. $jc_disable_field .' ></textarea>';
            $jc_chat_form .= '<p id="jheck_chat_message-counter" '.$jc_hide_field.'></p>';
            $jc_chat_form .= '</form>';
            $jc_chat_form .= '<button type="jheck-chat-submit-message" '.$jc_hide_field.' '. $jc_disable_field .'><i class="fa fa-paper-plane"></i> Send</button>';

            $jc_chat_form .= '<p class="jc_press_enter_to_submit" '.$jc_hide_field.' ><input id="jc_press_enter_to_submit_checkbox" type="checkbox" checked> <label for="jc_press_enter_to_submit_checkbox">Press enter to submit</label></p>';           
                    
            
            $jc_chat_form .= '<div id="jheck_chat_msg-holder"></div>';
           
            } /* if (jc_option_val( 'jheck_chat_user_login' ) && !is_user_logged_in()) { */

            $jc_chat_form .= '<div>'.wp_login_form( $jc_login_form_args ).'</div>';
            
            /**
             * Hidden fields settings default value
             * @since 1.4
             */
           
            $jc_chat_box_title = 'Chat';
            $jc_max_chatbox_message = 100;

            if (jc_option_val('jheck_chat_box_title')) {
                $jc_chat_box_title = jc_option_val('jheck_chat_box_title');
            }

            if (jc_option_val('jheck_chat_max_chatbox_message')) {
                $jc_max_chatbox_message = jc_option_val('jheck_chat_max_chatbox_message');
            }            

            /**
             * Hidden field settings
             * @since 1.4
             */
            
            $jc_chat_form .= '<div class="jc_hidden_fields">';

                $jc_chat_form .= '<input id="jc_chat_box_title" type="hidden" value="'.$jc_chat_box_title.'">';
                $jc_chat_form .= '<input id="jc_max_chatbox_message" type="hidden" value="'.$jc_max_chatbox_message.'">';
            
            $jc_chat_form .= '</div>';

            $jc_chat_form .= '<p style="font-size: 10px; margin: 0 auto 10px; text-align: right;"><a style="text-decoration: none;" href="https://wordpress.org/plugins/jheck-chat/?utm_source='.urldecode(home_url()).'&utm_medium=chatbox_footer&utm_campaign='.date('MY').'" target="_blank">Powered by: Jheck Chat</a></p>';

        } /*if (!jc_option_val( 'jheck_chat_activate_chat' )) {*/


        $jheck_chat_layout  = new Template(JC_URL_PATH.'template/'.JC_TEMPLATE_NAME.'/jc_layout.tpl');   
        $jheck_chat_layout->set("jc_chat_messages", $jheck_chat_content);
        $jheck_chat_layout->set("jc_chat_form_fields", $jc_chat_form);

        /**
         * Condition if page selected to display chat
         * @since 1.0
         */

        if ( (jc_option_val('jheck_chat_display_front_page') && is_front_page()) 
            || (jc_option_val('jheck_chat_display_home_page') && is_home())
            || (jc_option_val('jheck_chat_display_all_page') && is_page())
            || (jc_option_val('jheck_chat_display_post_page') && is_single())
            || (jc_option_val('jheck_chat_display_archive_page') && is_archive())
            || (jc_option_val('jheck_chat_display_search_page') && is_search())
            ) {

            if (!jc_option_val( 'jheck_chat_activate_chat' )) {
                if (is_user_logged_in() && $jc_user_role == 'administrator') {
                    echo '<div class="jheck_chat_main_wrapper">'.$jheck_chat_layout->output().'</div>';
                }
            }else{
                echo '<div class="jheck_chat_main_wrapper">'.$jheck_chat_layout->output().'</div>';   
            }
            
             
        }

    }

}