/* Set all variables here */

var jc_wrapper = jQuery('.jheck_chat_main_wrapper');
var jc_hidden_fields = jc_wrapper.find('.jc_hidden_fields');
var jc_toggle = jc_wrapper.find('.jc_header button');  
var jc_message_wrapper = jc_wrapper.find('.jc_message_wrapper');
var jc_form_fields_wrapper = jc_wrapper.find('.jc_form_fields_wrapper');
var jc_form = jc_wrapper.find('form#jheck_chat_form');
var jc_button = jc_form_fields_wrapper.find('button');
var clone_data = jc_message_wrapper.find('ul li').last();
var old_count_counter = jc_wrapper.find('.jheck_chat_old_count');  
var jc_chat_box_title = jc_hidden_fields.find('#jc_chat_box_title').val();

jQuery(document).ready(function() {   

  /*Scroll to bottom if new message*/
  jQuery(jc_wrapper).find('#jheck_chat_new_message_alert_view').on('click', function(){
    jQuery(jc_message_wrapper).animate({ scrollTop: jQuery(jc_message_wrapper.find('ul'))[0].scrollHeight}, 500);
    jQuery(this).fadeOut();
    return false;
  })   

  /*Textarea characters counter*/
  var text_max = parseInt(jc_hidden_fields.find('#jc_max_chatbox_message').val());  

  jQuery(jc_form_fields_wrapper).find('textarea').on('keyup',function(){

    var text_length = jQuery(this).val().length;
    var text_remaining = text_max - text_length;

    jQuery(this).next('#jheck_chat_message-counter').fadeIn().text(text_remaining + ' characters remaining');

      if (text_remaining <= -1) {
        jc_button.attr("disabled", true);
        jc_button.find('i.fa').removeClass('fa-check').addClass('fa-remove').removeClass('fa-paper-plane');
      }else{
        jc_button.attr("disabled", false);
        jc_button.find('i.fa').removeClass('fa-check').removeClass('fa-remove').addClass('fa-paper-plane');
      }

     

  });
  

  /*Avoid maximum text on textarea*/
  jQuery('#jheck_chat_message').keypress(function(e) {
    if (e.which < 0x20) {
      return;     // Do nothing
    }
    if (this.value.length == text_max) {
      e.preventDefault();
    } else if (this.value.length > text_max) {

      this.value = this.value.substring(0, text_max);

    }
  });

  /*Refresh counter*/
  setInterval(function (){ 

    jQuery.ajax({
      type: "post",
      url: '?jheck_chat_fetch_new_count',
      data: 'fetch_item=1',
      contentType: "application/x-www-form-urlencoded",
        success: function(response) {
          if (response['status'] == "success") { 
            if (old_count_counter.text() < response['msg_count']) {
              /**
               * Reload if there is new message.
               */              
              old_count_counter.text(response['msg_count']);
              jc_toggle.text('* New message - ' + jc_chat_box_title);
              jQuery(jc_wrapper).find('#jheck_chat_new_message_alert_view').fadeIn();
              jQuery(jc_message_wrapper.find('#jc_message_wrapper_content')).load(document.location.href + ' #jc_message');

            }


          } 
        },
        error: function(response) {
          jc_wrapper.find('#jheck_chat_msg-holder').fadeIn().empty().append('Something went wrong, Cant fetch messages');
          console.log('Something went wrong, Cant fetch messages');
        },
    });  

  }, 2000);

  /* If scrolled to bottom remove new message alert */

  jQuery(jc_message_wrapper).on('scroll', function() {
    if(jQuery(this).scrollTop() + jQuery(this).innerHeight() >= jQuery(this)[0].scrollHeight) {          
        jc_toggle.text(jc_chat_box_title);
        jQuery(jc_wrapper).find('#jheck_chat_new_message_alert_view').fadeOut();
      }
  })

  /* Desired name keyup script. */

  jc_wrapper.find('#jheck_chat_user').on('keyup', function(){

    if (jQuery(this).val().length >= 3) {
      jc_wrapper.find('textarea').fadeIn().attr('disabled', false);
      jc_button.fadeIn().attr('disabled', false);         
      jc_wrapper.find('#jheck_chat_msg-holder').fadeOut().empty();
      jc_wrapper.find('#jheck_chat_message-counter').fadeIn();
      jc_wrapper.find('.jc_press_enter_to_submit').fadeIn();      
      
    }else{
      jc_wrapper.find('textarea').fadeOut().attr('disabled', true);
      jc_button.fadeOut().attr('disabled', true);
      jc_wrapper.find('#jheck_chat_msg-holder').fadeIn().empty().append('Minimum of 3 characters.');
      jc_wrapper.find('#jheck_chat_message-counter').fadeOut();
      jc_wrapper.find('.jc_press_enter_to_submit').fadeOut();
    }    
  })


  /* Send message via enter */
  jQuery(jc_form_fields_wrapper).find('textarea').on('keydown', function(e) {
   if (jQuery('#jc_press_enter_to_submit_checkbox').is(':checked')){     

      if(e.which == 13) {        
        jc_send_message();
        return false;
      }

    }
  });


  /* Send message via button click*/

  jQuery(jc_button).on('click', function(){
    if (jQuery(this).attr('type') == 'jheck-chat-submit-message') {      
      jc_send_message();      
    }
    else{
      console.log('Invalid submit button');
    }
  });

  /* Login button clicked */

  jQuery('#jheck_chat_login_button').on('click', function(){
    jQuery('#jheck_chat_user').slideToggle();
    jQuery('#jheck_chat_loginform').slideToggle();
    return false;
  });

  jQuery(function(){

    /* Change chat title text */   
    
    jc_toggle.text(jc_chat_box_title);

  });

  /* Add class on chatbox wrapper on toggle button clicked*/

  jQuery(jc_toggle).on('click', function(){
    jc_wrapper.toggleClass('display-chatbox');
  });


});


/* CALL ALL FUNCTIONS HERE */

function jc_send_message(){

  jQuery(jc_button.find('i.fa')).removeClass('fa-check').removeClass('fa-remove').removeClass('fa-paper-plane').toggleClass('fa-spinner fa-spin');
 
  jQuery.ajax({
    type: "post",
    url: '?jheck_chat_send_message',
    data: 'send_message=1&' + jc_form.serialize(),
    contentType: "application/x-www-form-urlencoded",
      success: function(response) {
        if (response['status'] == "success") { 
          jc_wrapper.find('#jheck_chat_msg-holder').fadeOut().empty();

          if (clone_data.length == 0) {
            /* Reload message wrapper if no data yet. */
            jQuery(jc_message_wrapper.find('ul')).load(document.location.href + ' #jc_message_wrapper').fadeIn();
          }
          else{
            /* Append message if with data to clone. */
            var reponse_message = response['message'].replace(/\r\n|\n|\r/g, '<br />');
            var replace_data = clone_data.clone().appendTo(jc_message_wrapper.find('ul'));
            replace_data.find('.sender-name').html('You');
            replace_data.find('.sender-message').html(reponse_message);
            replace_data.find('.chat-date').text("just now");
          }

          old_count_counter.text(response['total_messages']);        

          jc_form_fields_wrapper.find('textarea').val('');
          jQuery(jc_button.find('i.fa')).addClass('fa-check').toggleClass('fa-spinner fa-spin');
          jQuery(jc_message_wrapper).animate({ scrollTop: jQuery(jc_message_wrapper.find('ul'))[0].scrollHeight}, 500);

        }
        else if(response['status'] == "error"){
          jQuery(jc_button.find('i.fa')).addClass('fa-remove').toggleClass('fa-spinner fa-spin');
          jc_wrapper.find('#jheck_chat_msg-holder').fadeIn().empty().append(response['msg']);
        }
      },
      error: function(response) {
        jQuery(jc_button.find('i.fa')).addClass('fa-remove').toggleClass('fa-spinner fa-spin');
        jc_wrapper.find('#jheck_chat_msg-holder').fadeIn().empty().append('Database connection error, Cant send new messages. Refresh your page');
        
      },
  }); 

}