<?php
/**
 * Process form
 * @since 1.0
 */

$table_name = JC_MYSQL_INBOX;

/**
 * Fetch total counts of messages.
 * @since 1.0
 */

if (isset($_GET['jheck_chat_fetch_new_count'])) {

	if (jc_is_ajax()) {

		/**
		 * Revised fetching of messages
		 * @since 1.3
		 */

		// $total_messages = count($wpdb->get_results( "SELECT * FROM $table_name"));
		$total_messages = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
	
		// echo '<span id="jheck_chat-new-count-total">'.$total_messages.'</span>';
		if (isset($_POST['fetch_item'])) {
			$response_array = array(
				'status' => 'success',
				'msg_count' => $total_messages,
			);
		}else{
			$response_array = array(
				'status' => 'error',
				'msg_count' => 0,
			);	
		}		

		header('Content-Type: application/json');
		die(json_encode( $response_array ));

	}
	else{

		/**
		 * Invalid request
		 */

		jc_invalid_request();		
	}
	
}


/**
 * Sending messages.
 * @since 1.0
 */
if (isset($_GET['jheck_chat_send_message'])) {

	if (jc_is_ajax()) {

		if (isset($_POST['send_message'])) {

			$user_name = 'Anonymous';

			if (isset($_POST['jheck_chat_user_encrypted'])) {
				$user_name = jc_decrypt($_POST['jheck_chat_user_encrypted']);
			}
			if (isset($_POST['jheck_chat_user'])) {
				$user_name = $_POST['jheck_chat_user'];
			}
			
			$message = jc_sanitize_content($_POST['jheck_chat_message']);
			$chat_date = jc_get_date();
			$ip = jc_get_ip();

			/**
			 * Get user ID
			 * @since 1.4
			 */

			$jc_user_id = '';
			if (isset($_POST['jheck_chat_user_id'])) {
				$jc_user_id = $_POST['jheck_chat_user_id'];
			}

			/**
			 * Unique name stored inside database.
			 * @since 1.1
			 */

			$jc_unique_name = '';
			if (isset($_POST['jheck_chat_user_unique'])) {
				$jc_unique_name = $_POST['jheck_chat_user_unique'];
			}

			/**
			 * Filter message
			 * @since 1.0
			 */
			$spam_detected = 0;

			if (jc_option_val('jheck_chat_filter_message')) {		

				$msg_arr = jc_option_val('jheck_chat_filter_message');
				$filter_msg = explode(',', $msg_arr);

				foreach ($filter_msg as $val) {
					if (strpos($message, $val) !== false) {
						$spam_detected++;

						$status_msg = 'Your message has been blocked, it contains word(s) that is/are blacklisted by administrator.';
					}
				}
			}

			/**
			 * Empty fields filter
			 * @since 1.0
			 */
			$empty_fields = 0;

			if (empty($user_name) || empty($message)) {
				$empty_fields++;
				$status_msg = 'Empty field, enter your message inside the chatbox area.';
			}

			/**
			 * Minimum text content
			 * @since 1.4
			 */

			$minimum_text_content = 0;
            $jc_min_chatbox_message = 3;

            if (jc_option_val('jheck_chat_min_chatbox_message')) {
                $jc_min_chatbox_message = jc_option_val('jheck_chat_min_chatbox_message');
            }
			

			if (strlen($message) < $jc_min_chatbox_message) {
				$minimum_text_content++;
				$status_msg = 'Minimum message content should be atleast '.$jc_min_chatbox_message.' letters';
			}

			/**
			 * Processing to send message.
			 * @since 1.0
			 */

			if (($spam_detected == 0) && ($empty_fields == 0) && ($minimum_text_content == 0)) {		

				if ( jc_insert_message($user_name,$jc_user_id,$message,$chat_date,$ip,$jc_unique_name) ) {	

					$total_messages = count($wpdb->get_results( "SELECT * FROM $table_name"));			

					$response_array = array(
						'status' => 'success',
						'message' => $_POST['jheck_chat_message'],
						'chat_date' => $chat_date,
						'total_messages' => $total_messages,
					);

					if (!empty($jc_unique_name)) {					
						$response_array['user_name'] = $user_name . ' <span class="jc_unique_name">('.$jc_unique_name.')</span>';
					}else{
						$response_array['user_name'] = $user_name;
					}

				}else{
					$response_array = array(
						'status' => 'error', 
						'msg' => 'Sending failed!',
					);
				}
				
			}else{
				$response_array = array(
					'status' => 'error', 
					'msg' => $status_msg,
				);
			}	
		}

		/**
		 * Return json result.
		 * @since 1.0
		 */
		header('Content-type: application/json');	
		die( json_encode($response_array) );
		
	}
	else{

		/**
		 * Invalid request
		 */
		jc_invalid_request();
	}
	
}
