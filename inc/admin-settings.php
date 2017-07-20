<?php 
/**
 * Vafpress plugin admin settings page
 * @since 1.0
 * @package Jheck Chat
 */

/**
 * Check if registration is enabled on wordpress.
 * @since 1.4
 */

if ( get_option( 'users_can_register' ) ) {
    $jheck_chat_display_registration_form_desc = 'Display registration link on chat form.';
}else{
	$jheck_chat_display_registration_form_desc = 'Registration is disabled, <a href="'.admin_url('options-general.php#users_can_register').'">enable</a> it here and make sure the role is subscriber only.';
}

new VP_Option(array(
        'is_dev_mode'           => false,
        'option_key'            => 'jheck_option',
        'page_slug'             => 'jheck_option',
        'template'              => 
        	array(
				'title' => __('Jheck Chat Settings', 'jheck_chat'),
				'menus' => 
					array(
						array(
						    'title' => __('Appearance', 'jheck_chat'),
						    'name' => 'jheck_chat_display_settings',
						    'icon' => 'font-awesome:fa-eye',
						    'controls' => 
						    	array(
						            'Display' => 
						            	array(
											'type' => 'section',
											'title' => __('Activate Chat', 'jheck_chat'),
											'fields' => 
												array(
												 	array(
														'type' => 'toggle',
														'name' => 'jheck_chat_activate_chat',
														'label' => __('Toggle to activate chat', 'jheck_chat'),
														'default' => '1',
													),							 	
												),
										),
										array(
											'type' => 'section',
											'title' => __('Chat box title', 'jheck_chat'),
											'fields' => 
												array(
												 	array(
														'type' => 'textbox',
														'name' => 'jheck_chat_box_title',
														'label' => __('Enter title you want to display on chatbox title', 'jheck_chat'),
														'default' => 'Chat',
													),							 	
												),
										),
						            	array(
											'type' => 'section',
											'title' => __('Toggle on what pages / posts you want to display chat icon', 'jheck_chat'),
											'fields' => 
												array(
												 	array(
														'type' => 'toggle',
														'name' => 'jheck_chat_display_front_page',
														'label' => __('FRONT PAGE', 'jheck_chat'),
														'description' => __('Small chat icon will display on front page', 'jheck_chat')
													),
													array(
														'type' => 'toggle',
														'name' => 'jheck_chat_display_home_page',
														'label' => __('HOME PAGE', 'jheck_chat'),
														'description' => __('Small chat icon will display on home page', 'jheck_chat')
													),
													array(
														'type' => 'toggle',
														'name' => 'jheck_chat_display_all_page',
														'label' => __('ALL PAGE', 'jheck_chat'),
														'description' => __('Small chat icon will display on all pages', 'jheck_chat'),
														'default' => '1',
													),	
													array(
														'type' => 'toggle',
														'name' => 'jheck_chat_display_post_page',
														'label' => __('POSTS', 'jheck_chat'),
														'description' => __('Small chat icon will display on all posts pages', 'jheck_chat'),
														'default' => '1',
													),
													array(
														'type' => 'toggle',
														'name' => 'jheck_chat_display_archive_page',
														'label' => __('ARCHIVE PAGES', 'jheck_chat'),
														'description' => __('Small chat icon will display on all archive pages (category,tags,date,etc.)', 'jheck_chat'),
														'default' => '0',
													),	
													array(
														'type' => 'toggle',
														'name' => 'jheck_chat_display_search_page',
														'label' => __('SEARCH PAGES', 'jheck_chat'),
														'description' => __('Small chat icon will display on search page.', 'jheck_chat'),
														'default' => '0',
													),					 	
												),
										),									
						        ),
						),
						array(
						    'title' => __('Templates', 'jheck_chat'),
						    'name' => 'jheck_chat_template_settings_main',
						    'icon' => 'font-awesome:fa-files-o',
						    'controls' => 
							    	array(
							            'Templates' => 						            	
											array(
											    'type' => 'radioimage',
											    'name' => 'jheck_chat_template',
											    'label' => __('Template', 'jheck_chat'),
											    'description' => __('By selecting default template, chat box will inherit the design based on your template.', 'jheck_chat'),
											    'item_max_width' => '120',
											    'item_max_height' => '400',        									
											    'items' => array(
											        'data' => array(
											            array(
											                'source' => 'function',
											                'value'  => 'jc_chat_box_template',
											            ),
											        ),
											    ),
											    'default' =>  
											    	array(
											            'default',
											        ),
											),						            									
							     	),		    	
						),						
						array(
						    'title' => __('Settings', 'jheck_chat'),
						    'name' => 'chat_settings',
						    'icon' => 'font-awesome:fa-cogs',
						    'controls' => 
						    	array(
						            'Settings' => 
							            array(
											'type' => 'section',
											'title' => __('Maximum number of message to load on chatbox.', 'jheck_chat'),
											'fields' => array(
														    array(
														        'type' => 'slider',
														        'name' => 'jheck_chat_max_chatbox',
														        'label' => __('Maximum number of chat to display', 'jheck_chat'),
														        'description' => __('Min. of 20 and Max. of 100 chat messeages per display.', 'jheck_chat'),
														        'min' => '20',
														        'max' => '100',
														        'step' => '1',
														        'default' => '25',
														    ),
														)
										),
										array(
											'type' => 'section',
											'title' => __('Message input', 'jheck_chat'),
											'fields' => 
												array(
													array(
												        'type' => 'slider',
												        'name' => 'jheck_chat_min_chatbox_message',
												        'label' => __('Minimum messages user can chat.', 'jheck_chat'),
												        'description' => __('Min. of 3 and Max. of 20 characters', 'jheck_chat'),
												        'min' => '3',
												        'max' => '20',
												        'step' => '1',
												        'default' => '3',
												    ),

												 	array(
												        'type' => 'slider',
												        'name' => 'jheck_chat_max_chatbox_message',
												        'label' => __('Maximum messages user can chat.', 'jheck_chat'),
												        'description' => __('Min. of 100 and Max. of 500 characters', 'jheck_chat'),
												        'min' => '100',
												        'max' => '500',
												        'step' => '1',
												        'default' => '100',
												    ),
												),
										),

						            	array(
											'type' => 'section',
											'title' => __('User settings', 'jheck_chat'),
											'fields' => 
												array(
												 	array(
														'type' => 'toggle',
														'name' => 'jheck_chat_user_login',
														'label' => __('User login', 'jheck_chat'),
														'description' => __('Only logged in user can use chat.', 'jheck_chat'),
														'default' => '0',
													),	
													array(
														'type' => 'toggle',
														'name' => 'jheck_chat_display_registration_form',
														'label' => __('Display registration link', 'jheck_chat'),
														'description' => __($jheck_chat_display_registration_form_desc, 'jheck_chat'),
														'default' => '0',
													),		 	
												),
										),

										array(
											'type' => 'section',
											'title' => __('Filter message', 'jheck_chat'),
											'fields' => 
												array(
												 	array(
														'type' => 'textarea',
														'name' => 'jheck_chat_filter_message',
														'label' => __('Enter keywords', 'jheck_chat'),
														'description' => __('Enter keywords you want to filter on messages. Use comma \',\' to separate words.', 'jheck_chat'),
														/*'default' => '0',*/
													),							 	
												),
										),

										array(
											'type' => 'section',
											'title' => __('Custom CSS', 'jheck_chat'),
											'fields' => array(
											    array(
											        'type' => 'codeeditor',
											        'name' => 'jheck_chat_custom_css',
											        'label' => __('Write your custom css here.', 'jheck_chat'),
											        'description' => __('If you know what you are doing. Enter your css code here.', 'jheck_chat'),
											        'theme' => 'chrome',
											        'mode' => 'css',
											    ),
											)
										),
								),						    	
						),
						array(
						    'title' => __('Support Us', 'jheck_chat'),
						    'name' => 'support_us',
						    'icon' => 'font-awesome:fa-gear',
						    'controls' => 
						    	array(
						            'Settings' => 
							            array(
											'type' => 'section',
											'title' => __('', 'jheck_chat'),
											'fields' => array(
														    array(
														        'type' => 'notebox',
														        'name' => 'support_us_note_1',
														        'label' => __('<i class="fa fa-facebook-square"></i> Like us on facebook', 'jheck_chat'),
														        'description' => __('<a target="_blank" href="https://www.facebook.com/JheckChat/">Facebook page link</a>'),
														        'status' => 'normal',
														    ),
														    array(
														        'type' => 'notebox',
														        'name' => 'support_us_note_2',
														        'label' => __('<i class="fa fa-coffee"></i> Buy us some coffee', 'jheck_chat'),
														        'description' => __('Buy us some coffee by donating <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=KLBDYB3DSPWCJ&lc=PH&item_name=Free%20Jheck%20Chat%20Plugin&item_number=Free%20Jheck%20Chat%20Plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted">here</a>.'),
														        'status' => 'normal',
														    ),
														)
										),						            	
								),						    	
						),

						

					),
			),

       'menu_page' => array(
			        'icon_url' => JC_URL . '/sources/img/jc-icon.png',
			        'position' => 99,
			    ),
        'use_auto_group_naming' => true,
        'use_util_menu'			=> false,
        'use_exim_menu'         => true,
        'minimum_role'          => 'edit_theme_options',
        'layout'                => 'fixed',
        'page_title'            => __( 'Jheck Chat', 'jheck_chat' ),
        'menu_label'            => __( 'Jheck Chat', 'jheck_chat' ),
    ));