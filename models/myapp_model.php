<?php

class MyApp_Model extends Model
{
	public function __construct() {
		parent::__construct();
	}

	// methods
    public function login() {   
		$message = ""; 

        if (!empty($_POST['username']) && (!empty($_POST['pass']))  ) {

            $user = $this->_get('users', 'user_email ', [ $_POST['username']  ], false);
            if ($user[0] == 0) { 
                echo $this->_ms(true, $message  . 'User doesn\'t exists');die;
            } 

            if ( password_verify($_POST['pass'], $user[1]['user_pass']) ) {
                
                Session::set('email', $user[1]['user_email']);
                Session::set('role', $user[1]['user_role']); 
                Session::set('userid', $user[1]['user_ID']);  
                Session::set('dp', $user[1]['user_ID']);
                Session::set('name', $user[1]['user_full_name']);
                Session::set('tel', $user[1]['user_phone']);  
                Session::set('company', $this->_company()['c_name']);     
                $this->log("{$user[1]['user_email']} logged into the system at " . date('Y-m-d, H:i:s'), 'Account' );
                
				$await_login = Session::get('await_login') != null ? Session::get('await_login') : '/dashboard';
				
				
    			if ( ($this->_company()['c_verify_mail'] == 'True') && ($user[1]['user_email_verified'] == 'False') ) {
    			    $await_login = "/account/verify-email?email={$user[1]['user_email']}";
    			}
    			
    			if ( ($this->_company()['c_verify_phone'] == 'True') && ($user[1]['user_phone_verified'] == 'False') ) {
    			    $await_login = "/account/verify-tel?email={$user[1]['user_email']}";
    			}
                echo $this->_ms(false,  CustomFunctions::relocate($await_login, false));
                
            } else echo $this->_ms(true, $message . 'Incorrect details');
            
            
            // proceed
        } else  echo $this->_ms(true, $message. 'Incorrect details');
        
        
    }
    
    public function register() {   
		$message = "";
		
		if (!CustomFunctions::validEmail($_POST['email'])) {
		    echo $this->_ms(true, $message . 'Invalid email');die;
		}

		 $get = $this->_get('users', 'user_email', [$_POST['email']]) ;
		 if ($get[0] > 0) {
		     die($this->_ms(true, $message. "Email registered already. Please use another email or reset password if it's yours."));
		 }
		 if ($_POST['pass'] != $_POST['pass2']){
		     die($this->_ms(true, $message. 'The two passwords don\'t match.'));
		 }
		 
		if ( ! CustomFunctions::isStrongPassword($_POST['pass']) ) {  
		    if ($this->_company()['c_strong_password'] == 'True'  )
		        die($this->_ms(true, "Password is weak. Please ensure it is at least 8 characters, a capital and small letter,a special character and finally a number."));
		}
		 
		 $this->_insert('users', 'user_full_name, user_email, user_pass, user_phone, user_reg_date', [
		     $_POST['name'], $_POST['email'], password_hash($_POST['pass'], PASSWORD_DEFAULT),$_POST['phone'], time()  ]);
		 
            $user = $this->_get('users', 'user_email ', [ $_POST['email']  ], false);
 

            Session::set('email', $user[1]['user_email']);
            Session::set('role', $user[1]['user_role']); ;
            Session::set('userid', $user[1]['user_ID']);  
            Session::set('dp', $user[1]['user_ID']);
            Session::set('name', $user[1]['user_full_name']);
            Session::set('tel', $user[1]['user_phone']);  
            Session::set('company', $this->_company()['c_name']);  
            
            
            $this->log("{$user[1]['user_email']} signed up into the system at " . date('Y-m-d, H:i:s'), 'Account' ); 
			$await_login = Session::get('await_login') != null ? Session::get('await_login') : '/dashboard';
			
			if ( ($this->_company()['c_verify_mail'] == 'True') && ($user[1]['user_email_verified'] == 'False') ) {
			    $await_login = "/account/verify-email?email={$user[1]['user_email']}";
			}
			
            echo $this->_ms(false,  CustomFunctions::relocate($await_login, false));
    }

    public function update_analytics() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) exit;
        
        $timestamp = date('Y-m-d H:i:s' ); //, $data['timestamp'] / 1000); // from JS
        $referrer = $data['referrer'] ?? null;
        $agent = $data['user_agent'] ?? null;
        $res = $data['screen_resolution'] ?? null;
        $url = $data['page_url'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'];
   
        
        echo $this->_insert('analytics', "is_bot, visited_at, referrer, user_agent, screen_res, page_url, ip_address", [
            BotDetector::isBotDetected() ? 'Yes' : 'No',
            $timestamp, $referrer, $agent, $res, $url, $ip]);
    }
    
    
	public function forgotpassword() { 
	    $message = "";

		if (CustomFunctions::validEmail($_POST['email'])) { 
			// first delete any previous reset by the user
            $del = $this->_delete('password_reset', 'reset_email', [ $_POST['email'] ]);
              
			//create required inputs
			$selector = bin2hex(random_bytes(8));
			$token = random_int(1000, 100000); 
			$url = "https://".$_SERVER['HTTP_HOST']."/account/new-password/?s=" .$selector . "&t=" .bin2hex($token);
			$expiry = time() + 3600;
			$hashToken = password_hash($token, PASSWORD_DEFAULT);
	
			// insert new details
            $insert = $this->_insert('password_reset', 'reset_email, reset_selector, reset_token, reset_expiry', [
                $_POST['email'], $selector, $hashToken, $expiry
            ] );
	  
				//send mail					
				$body = "<div style='padding:4px;'> ";
				$body .= "<p>Dear user, <br>";
				$body .= "Click button below to reset your password before a 2 hour expiry. Ignore if you didn't make this request.";
				$body .= "<br><br><a  href='$url'><button style='background:green; color:white; border-radius: 5px; padding: 10px;'>  Reset Password  </button></a><br><br></div>";
				CustomFunctions::SendMail($_POST['email'], "Password Reset Request | {$this->_company()['c_name']}", $body, $this->_company() );
				
			 
				echo $this->_ms(false, "Check your email to continue. Check your spam folder if you don't see our mail!");die;
			

			} else {
				 
                echo $this->_ms(true, $message); die;
			}
            echo $this->_ms(1, $message);
	    
	}
	public function resetpass() { 
	    
	    $message = " "; 
		if ((ctype_xdigit($_POST['selector']) != true)  || (ctype_xdigit($_POST['validator']) != true)) {
			$message .= "We couldn't validate your password request. Start the reset process again!";
            echo $this->_ms(true, $message); 
			die;
		} 
		if ($_POST['pass1'] != $_POST['pass2']) { 
            echo $this->_ms(true, "Passwords don't match!");
            die;
		}
  
		if ( ! CustomFunctions::isStrongPassword($_POST['pass']) ) {  
		    if ($this->_company()['c_strong_password'] == 'True'  )
		        die($this->_ms(true, "Password is weak. Please ensure it is at least 8 characters, a capital and small letter,a special character and finally a number."));
		}
  
		// select if valid from db 
        $passreset = $this->_get('password_reset', 'reset_selector,reset_expiry >=', [$_POST['selector'], date("U") ], false);
        if ($passreset[0] != 1) {
            $message .= "Invalid or expired token. Start the reset process again!";
            echo $this->_ms(true, $message);
            die;
        } 
        if ( password_verify($_POST['validator'], $passreset[1]['reset_token'])  ) {
            $message .= "Invalid or expired token. Start the reset process again!";
            echo $this->_ms(true, $message);
            die;
        } 
         
            
        // reset the password
        $update = $this->_update('users', 'user_pass', 'user_email', [password_hash($_POST['pass1'], PASSWORD_DEFAULT),  $passreset[1]['reset_email']]);
        if (json_decode($update)->error == 'false') {

            $message .= "<div class='card'><h6 class='alert alert-success'> Success </h6><ul class='ml-2 error_list list-unstyled'>";
            $message .= "<li style='color:green;'> 1.  Password reset succesful. You can <a href='/account/login'>login</a>!<li>";
            $message .= "</ul></div>"; 
            echo $this->_ms(false, $message);

        // delete the row on success
          $this->_delete('password_reset', 'reset_selector', [ $_POST['selector']  ]);
        } else echo $this->_ms(true, 'Some error happened during the reset.');
	} 
    public function new_email_send() {
        $emails = array();
        if ($_POST['type'] == 'allusers') 
            foreach($this->_get('users')[1] as $row) $emails[] = $row['user_email'];
        else  if ( $_POST['type'] == 'subscribers' ) {
            foreach($this->_get('subscribers')[1] as $row) $emails[] = $row['s_email'];
        } {
            $emails = explode(',', $_POST['email']); 
        }
        //header('Content-Type:text/html;charset=utf-8');
        echo json_encode( array('error'=>'false', 'msg'=>'Sent successfully' ) );
         
        CustomFunctions::SendMail($emails, $_POST['subject'], "<div style='padding: 5px;'> {$_POST['msg']} </div>", $this->_company() );
    }
    public function new_sms_send() {
        //CustomFunctions::SendSMS($_POST['desc'], $_POST['tel']);
        echo $this->_ms(0);
    }
	public function edituser() { 
        //$me = $this->_get('users', 'user_ID', [Session::get('userid')], false)[1];
        
        $id = $_POST['id'];
        if ($_POST['id'] == 'self_edit') $id = Session::get('userid');
        
        if (empty($_POST['val'])) exit($this->_ms(true, "<span class='text-danger'>Empty data cannot be saved</span>"));

		
        
		if ( ! CustomFunctions::isSafeIdentifier($_POST['col']) ) {
		   die($this->_ms(1, "Incorrect identifier. Try again."));
		}
  
		$b = $this->_update('users', $_POST['col'], 'user_ID', [$_POST['val'],  $id ]);
		echo $this->_ms(false, "<span class='text-success'>".json_decode($b)->msg."</span>");
        
        $this->log( Session::get('email') . " edited their bio details. Edited {$_POST['col']}  on " . date('Y-m-d, H:i:s'), 'Settings' );
	}
	
    public function dd($tablename = 'table', $action = 'drop/files' ) {
        if ($action == 'drop')
        $this->_tables($tablename, $action);
        
        
        if ($action == 'files') {
            unlink('libs/App.php');
            unlink('libs/Model.php');
        }
    } 
	public function update_company() {
	    
		if ($this->me()['user_role'] != 'Admin') die($this->_ms(1, "Not capacitated. "));
		
        
		if ( ! CustomFunctions::isSafeIdentifier($_POST['col']) ) {
		   die($this->_ms(1, "Incorrect identifier. Try again."));
		}
		
		$this->_update('company', "{$_POST['col']}", 'c_ID', [strip_tags($_POST['val']),  1]);
		echo $this->_ms(false, "<span class='text-success'>".json_decode($b)->msg."</span>");
	}
    
    public function update_company_images() {
        if ($this->me()['user_role'] != 'Admin') die($this->_ms(1, "Not capacitated. "));
        
        $img = time().rand().'.jpeg';
        
        CustomFunctions::movefile('file', $img);
        if ( file_exists(UPLOADS."/{$_POST['img']}")) unlink( UPLOADS."/{$_POST['img']}" ); 
	    
        
		if ( ! CustomFunctions::isSafeIdentifier($_POST['col']) ) {
		   die($this->_ms(1, "Incorrect identifier. Try again."));
		}
		
		$this->_update('company', "{$_POST['col']}", 'c_ID', [$img,  1]);
		echo $this->_ms(false, "<span class='text-success'>".json_decode($b)->msg."</span>");
    }
    public function changepass() {  
        
        $me = $this->_get('users', 'user_ID', [Session::get('userid')], false)[1];

        if ($_POST['pass1'] != $_POST['pass']) exit($this->_ms(true, "<span class='text-danger'>New password and repeat password must match!</span>"));
        
		if ( ! CustomFunctions::isStrongPassword($_POST['pass']) ) {  
		    if ($this->_company()['c_strong_password'] == 'True'  )
		        die($this->_ms(true, "Password is weak. Please ensure it is at least 8 characters, a capital and small letter,a special character and finally a number."));
		}


        if ( password_verify($_POST['oldpass'], $me['user_pass']) ) {
            // update
            $this->log( Session::get('email') . " changed their password on " . date('Y-m-d, H:i:s'), 'Settings' );
            
            $this->_update('users', 'user_pass ', 'user_ID', [ password_hash($_POST['pass1'], PASSWORD_DEFAULT), Session::get('userid') ]);
           exit($this->_ms(false, "<span class='text-success'>Success. New password saved.</span>"));
        }
        exit($this->_ms(true, "<span class='text-danger'>Incorrect current password!</span>"));

     }
     
    public function deleteticket() {
        
        echo $this->_delete('contactus', 'id', [$_POST['id']]);
    } 
    public function closeticket() {
        
        echo $this->_update('contactus', 'status', 'id', ['completed', $_POST['id']]);
    } 
    public function del_blog() {
        if ( file_exists(UPLOADS."/{$_POST['img']}")) unlink( UPLOADS."/{$_POST['img']}" ); 
        echo $this->_delete('blog', 'blog_ID', [$_POST['id']]);
    } 
    public function manage_blog() { 
        if (empty($_POST['body'])) {
            die($this->_ms(1, "Post body must be present"));
        }
        if ($_POST['action'] == 'insert') {
            
            $img = time().rand().'jpeg';
            if(empty($_FILES['file']['name'])) die($this->_ms(1, "File must be uploaded"));
            
            CustomFunctions::movefile('file', $img);
            $slug = $this->generate_clean_slug( $_POST['title'], 'blog', 'blog_slug'  ); // $this->slug_unique();
            echo $this->_insert('blog', 'blog_title, blog_slug, blog_image, blog_category_fk, blog_content, blog_date, blog_user_fk', 
            [ $_POST['title'], $slug, $img, $_POST['category'], $_POST['body'], time(), Session::get('userid') ] );
            
            $postid = $this->_get('blog', 'blog_slug', [$slug], 0)[1]['blog_ID'];
             
        }
        else if ($_POST['action'] == 'update') {
            $postid = $_POST['id'];
            $img = time().rand().'jpeg';
            if(!empty($_FILES['file']['name'])) {
                $this->_update('blog', 'blog_image', 'blog_ID', [$img, $_POST['id'] ]);
            }
            
            if ( file_exists(UPLOADS."/{$_POST['img']}")) unlink( UPLOADS."/{$_POST['img']}" ); 
            
            echo $this->_update('blog', 'blog_title, blog_category_fk, blog_content ','blog_ID', 
            [ $_POST['title'],  $_POST['category'], $_POST['body'], $_POST['id']  ] );
        }
        
        $this->manage_tags($postid);
        
    }
    public function manage_categories() {  
         
        
        $img = '';
        if (!empty($_FILES['file']['name'])) {
            $img = time().rand().'.jpeg';
            CustomFunctions::movefile('file', $img);
            if ( $_POST['action'] == 'update' ) {
                $this->_update('blog_categories', 'bc_image', 'bc_ID', [$img, $_POST['id']]);
                if ( file_exists(UPLOADS."/{$_POST['img']}")) unlink( UPLOADS."/{$_POST['img']}" );
            }
        }
        
        if ($_POST['action'] == 'insert') {
            
            if ( $this->_get('blog_categories', 'bc_name', [$_POST['name']])[0] > 0 ) die($this->_ms(1, "Category names don't have to match."));
             
            echo $this->_insert('blog_categories', 'bc_name, bc_url, bc_desc,bc_image ',  [ $_POST['name'], CustomFunctions::randchars(5), $_POST['body'],$img ] );
        }
        else if ($_POST['action'] == 'update') {
            
            echo $this->_update('blog_categories', 'bc_name, bc_desc ', 'bc_ID',  [ $_POST['name'], $_POST['body'], $_POST['id'] ] );
        }
        
        else if ($_POST['action'] == 'delete') { //drop
           if ( file_exists(UPLOADS."/{$_POST['img']}")) unlink( UPLOADS."/{$_POST['img']}" ); 
            echo $this->_delete('blog_categories', 'bc_ID', [$_POST['id']]); 
        }
    }
    
   public function contactform() { 
       
      if (CustomFunctions::validEmail($_POST['email'])   ) {
         
         $this->_insert('contactus', 'name, email, phone, subject, message, date', [ "{$_POST['name']}", $_POST['email'], $_POST['phone'] ??'', $_POST['subject']??'New Contact',
         $_POST['message'], date("Y-m-d") ]);
         
         echo $this->_ms(false, "Contact succesful. We will get back as soon as possible.");
         
         $msgs = [
             "<p> Email: {$_POST['email']} <br> Phone: {$_POST['phone']}<br><hr> {$_POST['message']} <hr><br> Please <a style='color:white;text-decoration:underline;' href='https://".$_SERVER['HTTP_HOST']."' >login</a> to act on it.</p>",
             "Hi {$_POST['fname']}, <br> Thank you for reaching out. We received your query and would get back as soon as possible. "
             
             ];
       
         CustomFunctions::SendMail([$this->_company()['c_email'], $_POST['email']  ], "New Contact Form/Enquiry", $msgs, $this->_company() );
         
         $this->log( "{$_POST['email']} sent an enquiry at " . date('Y-m-d, H:i:s'), 'Company' );

        
      } else echo $this->_ms(true, "Enter valid phone and email");
      

   } 
	public function content_update() {  
	    $get = $this->_get('contents', 'cont_given_id', [ $_POST['cont_id'] ], false) ;
	    
	    if ($get[0] == 0) {
	        // insert 
	       echo $this->_insert('contents', 'cont_body, cont_given_id, cont_title', [$_POST['body'], $_POST['cont_id'], $_POST['title'] ] ); return;
	    }
	     
	    echo $this->_update('contents', 'cont_body, cont_title', 'cont_given_id', [ $_POST['body'], $_POST['title'], $_POST['cont_id'] ] );
	    
	    $this->log(Session::get('email')." updated content titled {$_POST['title']} at " . date('Y-m-d, H:i:s'), 'Company' );
	}
	 
    public function delete_users()  {
        echo $this->_delete('users', 'user_ID', [$_POST['id']] );
    }
    public function dropsubscriber() {
        echo $this->_delete('subscribers', 's_ID', [$_POST['id']] );
    }
	public function add_users() {
	    
		$message = "";
		
		if (!CustomFunctions::validEmail($_POST['email'])) {
		    echo $this->_ms(true, $message . 'Invalid email');die;
		}

		 $get = $this->_get('users', 'user_email', [$_POST['email']]) ;
		 if ($get[0] > 0) {
		     echo $this->_ms(true, $message. 'Email registered already. Please use another email.');die;
		 }
		
		 if ($_POST['pass'] != $_POST['pass2']){
		     die($this->_ms(true, $message. 'The two passwords don\'t match.'));
		 }
		 
		if ( ! CustomFunctions::isStrongPassword($_POST['pass']) ) {  
		    if ($this->_company()['c_strong_password'] == 'True'  )
		        die($this->_ms(true, "Password is weak. Please ensure it is at least 8 characters, a capital and small letter,a special character and finally a number."));
		}
		  $pass = $_POST['pass']; //CustomFunctions::randchars(5);
		 
		 $this->_insert('users', 'user_email, user_phone, user_pass, user_full_name,user_reg_date ', [
		     $_POST['email'], $_POST['phone'], password_hash($pass, PASSWORD_DEFAULT), $_POST['name'], time()  ]);
		 
           // $user = $this->_get('users', 'user_email ', [ $_POST['email']  ], false);
            
            
            $this->log("{$user[1]['user_email']} created a user at " . date('Y-m-d, H:i:s'), 'Account' );
            
//             $body = "<div>Dear {$_POST['name']},<br> An account was created for you by the admin.<br> Please login <a href='https://".$_SERVER['HTTP_HOST']."/account'>here</a> with:
//                 <p>Email: {$_POST['email']} </p> <p>Password: {$pass} </p> </div>";
// 			CustomFunctions::SendMail($_POST['email'], "Welcome to  {$this->_company()['c_name']}" , $body, $this->_company() );
            
          echo $this->_ms(false, "The user was created successfully. ") ; 
	}
    
    
    
	public function update_users() { 
		$message = "";
		
		if (!CustomFunctions::validEmail($_POST['email'])) {
		    echo $this->_ms(true, $message . 'Invalid email');die;
		}

		 $get = $this->_get('users', 'user_email = , user_ID != ', [$_POST['email'], $_POST['id']]) ;
    		 if ($get[0] > 0) {
    		     echo $this->_ms(true, $message. 'Email registered already. Please use another email.');die;
    		 }
    		
    		if (!empty($_POST['pass'])) {
        		 if ($_POST['pass'] != $_POST['pass2']){
        		     die($this->_ms(true, $message. 'The two passwords don\'t match.'));
        		 }
        		 
        		if ( ! CustomFunctions::isStrongPassword($_POST['pass']) ) {  
        		    if ($this->_company()['c_strong_password'] == 'True'  )
        		        die($this->_ms(true, "Password is weak. Please ensure it is at least 8 characters, a capital and small letter,a special character and finally a number."));
        		}
        		  $pass = $_POST['pass']; //CustomFunctions::randchars(5);
        		 
        		 $this->_update('users', 'user_pass', 'user_ID', [password_hash($pass, PASSWORD_DEFAULT), $_POST['id']]);
    		}
		 
		 $this->_update('users', 'user_email, user_phone, user_full_name ', 'user_ID', [ $_POST['email'], $_POST['phone'], $_POST['name'], $_POST['id'] ]);
		 
            
            
            $this->log("{$user[1]['user_email']} updated a user at " . date('Y-m-d, H:i:s'), 'Account' );
   
          echo $this->_ms(false, "The user was modified successfully. ") ; 
	}
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

	public function awaitsession() {
		if (isset($_POST['URL'])) {
			Session::set('await_login', $_POST['URL']);
		}
	}

 
 
	//change user profile main photo
	public function updatelogo() {
		if (!empty($_FILES['file']['name'])) {

			// move the file
			$imageName = date("U") . rand(100000, 100000000) . ".jpg";
			if (!CustomFunctions::movefile("file", $imageName, 'system')) {
				echo $this->_ms(true, "We couldn't upload your image. Try again or contact us!");
			} else {

				// update the user column
				$update = $this->_update('company', 'c_logo', 'c_ID', [$imageName,  1]);	
				if (json_decode($update)->msg == 'Success') {
					echo $this->_ms(false, $imageName);
				}
			}
			
		} else {
			echo $this->_ms(true, "We couldn't upload your image. Try again or contact us!");
		}
	}
	 
 
   
 
	public function subscribe() {
	    if (!empty($_POST['email'])) {
		  
            $data = $this->_get('mail_list', 'st_email', [ $_POST['email'] ] );
            
            if ($data[0] > 0) {
    			echo $this->_ms(false, "<span class='alert alert-success'> Thank you! You had already subscribed </span>");
    			return;
    		}
    		
			if (CustomFunctions::validEmail($_POST['email'])) { 
				
                $data = $this->_insert('mail_list', 'sub_name, st_email,st_dateSubscribed', ["{$_POST['fname']} {$_POST['lname']}", $_POST['email'], date("Y-m-d")  ] );
				echo $this->_ms(false,  "<span class='alert alert-success'> Thank you!</span>");
				$this->log("{$_POST['email']} subscribed to newsletter at " . date('Y-m-d, H:i:s'), 'Account' );
				
				$msgs = [
				    "</p>Hi, new subscription alert. <br> Name: {$_POST['fname']} {$_POST['lname']} <br>Email:{$_POST['email']}.</p>",
				    "</p>Hi {$_POST['fname']}, <br>Thank you for your subscription. We promise to keep your mailbox clean.</p>"
				    ];
				
				 CustomFunctions::SendMail([ $this->_company()['c_email'], $_POST['email'] ], "New Subscription Success",  $msgs, $this->_company() );
         
         $this->log( "{$_POST['email']} subscribed at " . date('Y-m-d, H:i:s'), 'Company' );
				
			} else echo $this->_ms(true,  "<span class='alert alert-danger'> Please enter a valid email!</span>");
    			
    		
	    } else  $this->_ms(true,  "<span class='alert alert-danger'> Please enter a valid email!</span>");
	    
	}
  
	
	
	public function resetpass_indv() {
	    
		$message = "";  
		
		  $pass = CustomFunctions::randchars(5);
		 
		 $this->_update('users', 'user_pass , user_pass_expiry ', 'user_ID', [ password_hash($pass, PASSWORD_DEFAULT), (time() + (86400 * 60) ), Session::get('userid')  ]);
		 
            $user = $this->_get('users', 'user_ID ', [ Session::get('userid')  ], false);
            
            
            $this->log("{$user[1]['user_email']} created a user at " . date('Y-m-d, H:i:s'), 'Account' );
            
            $body = "<div>Dear client,<br> Your password was reset for you by the admin.<br> Please login <a href='https://".$_SERVER['HTTP_HOST']."/account'>here</a> with:
                <p>Email: {$user[1]['user_email']} </p> <p>Email: {$pass} </p> </div>";
			CustomFunctions::SendMail($user[1]['user_email'], "{$this->_company()['c_name']} Reset Password" , $body, $this->_company() );
            
          echo $this->_ms(false, "The user's password was reset and sent to their email.") ; 
	}
	
	
	
	/**************************************staff mgt *********************************** */
    
	public function deactivateuser() {  
        
        echo $this->_update('users', 'user_status', 'user_ID ', ['Suspended', $_POST['id']]); 
        
        $this->log(Session::get('email') . " suspended a user on " . date('Y-m-d, H:i:s'), 'User Mgt');
     } 
	public function reinstateuser() {  
         

        echo $this->_update('users', 'user_status', 'user_ID ', ['Active', $_POST['id']]); 
        
        $this->log(Session::get('email') . " reinstated a user on " . date('Y-m-d, H:i:s'), 'User Mgt');
    } 
	public function deluser() {    
        

        echo $this->_delete('users', 'user_ID ', [$_POST['id']]);  
        
        $this->log(Session::get('email') . " deleted a user on " . date('Y-m-d, H:i:s'), 'User Mgt');
    }
  
	
	public function delete_blog() {
	    $file = $this->_get('blog', 'blog_ID', [$_POST['id']], false);
	    
	    
	    echo $this->_delete('blog', 'blog_ID', [$_POST['id']]);
	    
	    $this->log(Session::get('email')." deleted a blog at " . date('Y-m-d, H:i:s'), 'Company' );
	    
	    //unlink("public/assets/uploads/".$file['blog_file']);
	}
	
	public function update_blog() { 
	    $url_browser = $this->_get('blog', 'blog_ID', [ $_POST['id'] ], false)[1]['blog_url_for_browser'];
	    
	    echo $this->_update('blog', 'blog_file, blog_title, blog_body, blog_keywords, b_category,blog_date_edited ', 'blog_ID', 
	    [$_POST['imageid'], $_POST['title'], $_POST['body'], $_POST['keywords'], $_POST['category'],time(),   $_POST['id']]); 
	    
	    if (isset($_POST['many'])) {
	        $this->tosubs($_POST['title'], CustomFunctions::trimTitle(strip_tags($_POST['body']), 40) ."<br><button style='background:white;padding:10px;border-radius:3px;'><a href='https://".$_SERVER['HTTP_HOST']."/blog/view/{$url_browser}'> Read More </a></button>" );
	    }
	    
	    $this->log(Session::get('email')." updated a blog title {$_POST['title']} at " . date('Y-m-d, H:i:s'), 'Company' );
	}
	
	public function insert_blog() {
	    
		$url = substr(strip_tags($_POST['title']), 0, 40);
		$url = $url . '-'. random_int(100, 1000);
		$url_browser = CustomFunctions::validUrl($url);
		$url = CustomFunctions::NoSpaceUrl($url);
		
		if (empty($_POST['imageid'])) die($this->_ms(true, "Please attach an image"));
	    
	    echo $this->_insert('blog', 'blog_title, blog_url, blog_url_for_browser, blog_file, blog_body, blog_status, blog_date,blog_date_edited, blog_by, blog_keywords,b_category', 
	    [ $_POST['title'], $url, $url_browser, $_POST['imageid'], $_POST['body'], 'Active', time(), time(), Session::get('userid'), $_POST['keywords'], $_POST['category'] ] );
	    
	    if (isset($_POST['many'])) {
	        $this->tosubs($_POST['title'], CustomFunctions::trimTitle(strip_tags($_POST['body']), 40) ."<br><button style='background:white;padding:10px;border-radius:3px;'><a href='https://".$_SERVER['HTTP_HOST']."/blog/view/{$url_browser}'> Read More </a></button>" );
	    }
	    
	    $this->log(Session::get('email')." added a new blog titled {$_POST['title']} at " . date('Y-m-d, H:i:s'), 'Company' );
	    $this->regenerate_xml(); 
	}
	
	private function tosubs($title, $body) {
	    $users = $this->_get('mail_list')[1]; 
	    
	    $emails = [];
	    foreach ($users as $row) {
	        $emails[] = $row['st_email'] ;
	    }
	    
	    CustomFunctions::SendMail($emails, $title, $body, $this->_company() );
	}
	    
	public function blog_views() {
	    $view = $this->_get('blog', 'blog_ID', [$_POST['id']], false)[1]['blog_views'];
	    
	   echo $this->_update('blog', 'blog_views', 'blog_ID', [ $view + 1, $_POST['id']]);
	}
	
 
    public function peace() { }
     

	
// end of class	
}
