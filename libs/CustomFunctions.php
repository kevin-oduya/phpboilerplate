<?php
class CustomFunctions {

    public function __construct() {
    
    }

    public static function containsAnyWord(string $sentence, array $words): bool {
        $pattern = '/\b(' . implode('|', array_map('preg_quote', $words)) . ')\b/i';
        return (bool) preg_match($pattern, $sentence);
    }
    public static function replaceLinksWithAnchors($text) {
        return preg_replace_callback(
            '#\bhttps?://[^\s<>()]+#i',
            function($matches) {
                $url = $matches[0];
                $host = parse_url($url, PHP_URL_HOST);
    
                // Extract domain (e.g., youtube.com → Youtube)
                $domainParts = explode('.', $host);
                $mainDomain = $domainParts[count($domainParts) - 2] ?? 'Website';
                $display = ucfirst($mainDomain); // Capitalize first letter
    
                return "<a href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\">View $display page</a>";
            },
            $text
        );
    }
	
    public static function formatPhone(string $phone): string {
        // Remove any spaces or hyphens first
        $digits = preg_replace('/\D+/', '', $phone);
    
        // Ensure it starts with +254
        if (strpos($phone, '+') === 0) {
            $digits = '+' . $digits;
        }
    
        // Example: +254712345678  →  +254 712 345 678
        if (preg_match('/^\+254(\d{3})(\d{3})(\d{3})$/', $digits, $m)) {
            return "+254 {$m[1]} {$m[2]} {$m[3]}";
        }
    
        // If it's 07XXXXXXXX  →  07X XXX XXX
        if (preg_match('/^0(\d{3})(\d{3})(\d{3})$/', $digits, $m)) {
            return "0{$m[1]} {$m[2]} {$m[3]}";
        }
    
        // If pattern didn't match, return raw
        return $phone;
    }
    public static function isSafeIdentifier(string $name): bool {
        // 1. Must be valid UTF-8
        if (!mb_check_encoding($name, 'UTF-8')) {
            return false;
        }
    
        // 2. Must not contain null bytes or control characters (ASCII < 32 or 127)
        if (preg_match('/[\x00-\x1F\x7F]/', $name)) {
            return false;
        }
    
        // 3. Must match allowed characters: a-z, A-Z, 0-9, underscore (no leading numbers!)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            return false;
        }
    
        // 4. Optional: prevent overly long names (DB limits: 64 for MySQL identifiers)
        if (strlen($name) > 64) {
            return false;
        }
    
        // 5. Optional: block SQL reserved words (very basic list; customize per DB)
        $reserved = [
            'select', 'from', 'where', 'insert', 'delete', 'update',
            'drop', 'alter', 'table', 'join', 'union', 'into', 'create'
        ];
        if (in_array(strtolower($name), $reserved, true)) {
            return false;
        }
    
        return true;
    }

   public static function editBtn($uniqueid) {
        if ((Session::get('role') != null) && (Session::get('role') == 'Admin') ) {
            return "<button class='editBtn ' rel='$uniqueid'><i class='fa fa-pencil' style='cursor:pointer;'></i> Edit</button>";
        }
        
        return '';
    }
     public static function getRoundPrice($val) {

        $val = (int) $val;
        $return = $val / 1000000;
        return $return;
    }
    public static function validEmail($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
			return true;		
		return false;		
    }
    public static function verifyTel($tel) {   

        // returning true means an error
        $length = strlen($tel);

        if (!is_numeric($tel)) {
            return false; 
        } else {    
            if ( (!preg_match("/^[0-9+-]*$/", $tel)) || (($length < 10) || $length > 16) ) 
                return false;            
            return true;  
        }      
    
    }
    public static function _vaildUrl($url) {
        $url = trim($url);
        $url = strtolower($url);
        $url = str_replace(' ', '-', $url);
        return preg_replace('/[^A-Za-z0-9\_]/', '', $url);
    }
    public static function randchars($len) {
        $str = '';
       $alphabet = "a1AbBcCd2DeEfFg3GhHiIjJ4kKlLm56MnNoOpPqQr7RsStYu8UvVwWxX8yYz0Z";
       $b = str_split($alphabet);
       shuffle($b);

       for ($i = 1; $i <= $len ; $i++) { 
           $str .= $b[rand(0, strlen($alphabet) - 1)];
       }
       return $str;
   }
   public static function randomKey($length = null) {
    // creates a unique key for this customer
    $stringFrom = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRESTUVWXYZ_"; // allowed chars in the password
        if ($length == "" OR !is_numeric($length)){
        $length = 8; 
    }

    //srand(make_seed());

    $i = 0; 
    $password = "";    
    while ($i < $length) { 
        $char = substr($stringFrom, rand(0, strlen($stringFrom) - 1 ), 1);
        if (!strstr($password, $char)) { 
            $password .= $char;
            $i++;
        }
    }
    return $password;
}

    public static function isStrongPassword($password) {
        // Minimum 8 characters
        if (strlen($password) < 8) {
            return false;
        }
    
        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
    
        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
    
        // At least one digit
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
    
        // At least one special character
        if (!preg_match('/[\W_]/', $password)) {
            return false;
        }
    
        return true;
    }
    public static function vaildUrl($url) {
        $url = trim($url);
        $url = strtolower($url);
        $url = str_replace(' ', '-', $url);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $url);
    }
    public static function SendSMS($msg, $phone) {
        
    }
    public static function verifyImage($image) {
        $array = array("jpg", "jpeg", "png", "gif");
        $exp = explode(".", $image);
        $ext = strtolower(end($exp));
    
        if (in_array($ext, $array)) 
            return true;
        else
            return false;
        
    }
    public static function _styles() {
		return " <style> table{border-collapse:collapse}.table{width:100%;margin-bottom:1rem;color:#212529}.table td,.table th{padding:.75rem;vertical-align:top;border-top:1px solid #dee2e6}.table thead th{vertical-align:bottom;border-bottom:2px solid #dee2e6}.table tbody+tbody{border-top:2px solid #dee2e6}.table-sm td,.table-sm th{padding:.3rem}.table-bordered{border:1px solid #dee2e6}.table-bordered td,.table-bordered th{border:1px solid #dee2e6}.table-bordered thead td,.table-bordered thead th{border-bottom-width:2px}.table-borderless tbody+tbody,.table-borderless td,.table-borderless th,.table-borderless thead th{border:0}.table-striped tbody tr:nth-of-type(odd){background-color:rgba(0,0,0,.05)}.table-hover tbody tr:hover{color:#212529;background-color:rgba(0,0,0,.075)}.table-primary,.table-primary>td,.table-primary>th{background-color:#b8daff}.table-primary tbody+tbody,.table-primary td,.table-primary th,.table-primary thead th{border-color:#7abaff}.table-hover .table-primary:hover{background-color:#9fcdff}.table-hover .table-primary:hover>td,.table-hover .table-primary:hover>th{background-color:#9fcdff}.table-secondary,.table-secondary>td,.table-secondary>th{background-color:#d6d8db}.table-secondary tbody+tbody,.table-secondary td,.table-secondary th,.table-secondary thead th{border-color:#b3b7bb}.table-hover .table-secondary:hover{background-color:#c8cbcf}.table-hover .table-secondary:hover>td,.table-hover .table-secondary:hover>th{background-color:#c8cbcf}.table-success,.table-success>td,.table-success>th{background-color:#c3e6cb}.table-success tbody+tbody,.table-success td,.table-success th,.table-success thead th{border-color:#8fd19e}.table-hover .table-success:hover{background-color:#b1dfbb}.table-hover .table-success:hover>td,.table-hover .table-success:hover>th{background-color:#b1dfbb}.table-info,.table-info>td,.table-info>th{background-color:#bee5eb}.table-info tbody+tbody,.table-info td,.table-info th,.table-info thead th{border-color:#86cfda}.table-hover .table-info:hover{background-color:#abdde5}.table-hover .table-info:hover>td,.table-hover .table-info:hover>th{background-color:#abdde5}.table-warning,.table-warning>td,.table-warning>th{background-color:#ffeeba}.table-warning tbody+tbody,.table-warning td,.table-warning th,.table-warning thead th{border-color:#ffdf7e}.table-hover .table-warning:hover{background-color:#ffe8a1}.table-hover .table-warning:hover>td,.table-hover .table-warning:hover>th{background-color:#ffe8a1}.table-danger,.table-danger>td,.table-danger>th{background-color:#f5c6cb}.table-danger tbody+tbody,.table-danger td,.table-danger th,.table-danger thead th{border-color:#ed969e}.table-hover .table-danger:hover{background-color:#f1b0b7}.table-hover .table-danger:hover>td,.table-hover .table-danger:hover>th{background-color:#f1b0b7}.table-light,.table-light>td,.table-light>th{background-color:#fdfdfe}.table-light tbody+tbody,.table-light td,.table-light th,.table-light thead th{border-color:#fbfcfc}.table-hover .table-light:hover{background-color:#ececf6}.table-hover .table-light:hover>td,.table-hover .table-light:hover>th{background-color:#ececf6}.table-dark,.table-dark>td,.table-dark>th{background-color:#c6c8ca}.table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#95999c}.table-hover .table-dark:hover{background-color:#b9bbbe}.table-hover .table-dark:hover>td,.table-hover .table-dark:hover>th{background-color:#b9bbbe}.table-active,.table-active>td,.table-active>th{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover>td,.table-hover .table-active:hover>th{background-color:rgba(0,0,0,.075)}.table .thead-dark th{color:#fff;background-color:#343a40;border-color:#454d55}.table .thead-light th{color:#495057;background-color:#e9ecef;border-color:#dee2e6}.table-dark{color:#fff;background-color:#343a40}.table-dark td,.table-dark th,.table-dark thead th{border-color:#454d55}.table-dark.table-bordered{border:0}.table-dark.table-striped tbody tr:nth-of-type(odd){background-color:rgba(255,255,255,.05)}.table-dark.table-hover tbody tr:hover{color:#fff;background-color:rgba(255,255,255,.075)}@media (max-width:575.98px){.table-responsive-sm{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-sm>.table-bordered{border:0}}@media (max-width:767.98px){.table-responsive-md{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-md>.table-bordered{border:0}}@media (max-width:991.98px){.table-responsive-lg{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-lg>.table-bordered{border:0}}@media (max-width:1199.98px){.table-responsive-xl{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-xl>.table-bordered{border:0}}.table-responsive{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive>.table-bordered{border:0}.list-unstyled{padding-left:0;list-style:none;}.container{width:100%;padding-right: 15px;padding-left: 15px;margin-right:auto;margin-left:auto;}.alert{position:relative;padding:0.75rem 1.25rem;margin-bottom:1rem;border:1pxsolid transparent;border-radius: 0.25rem;}.row{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-right:-15px;margin-left:-15px}.col,.col-1,.col-10,.col-11,.col-12,.col-2,.col-3,.col-4,.col-5,.col-6,.col-7,.col-8,.col-9,.col-auto,.col-lg,.col-lg-1,.col-lg-10,.col-lg-11,.col-lg-12,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9,.col-lg-auto,.col-md,.col-md-1,.col-md-10,.col-md-11,.col-md-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9,.col-md-auto,.col-sm,.col-sm-1,.col-sm-10,.col-sm-11,.col-sm-12,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-auto,.col-xl,.col-xl-1,.col-xl-10,.col-xl-11,.col-xl-12,.col-xl-2,.col-xl-3,.col-xl-4,.col-xl-5,.col-xl-6,.col-xl-7,.col-xl-8,.col-xl-9,.col-xl-auto{position:relative;width:100%;padding-right:15px;padding-left:15px}.col-md-3{-ms-flex:0 0 25%;flex:0 0 25%;max-width:25%}.col-md-4{-ms-flex:0 0 33.333333%;flex:0 0 33.333333%;max-width:33.333333%}.list-group{display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;padding-left:0;margin-bottom:0}.list-group-item-action{width:100%;color:#495057;text-align:inherit}.list-group-item-action:focus,.list-group-item-action:hover{z-index:1;color:#495057;text-decoration:none;background-color:#f8f9fa}.list-group-item-action:active{color:#212529;background-color:#e9ecef}.list-group-item{position:relative;display:block;padding:.75rem 1.25rem;margin-bottom:-1px;background-color:#fff;border:1px solid rgba(0,0,0,.125)}.list-group-item:first-child{border-top-left-radius:.25rem;border-top-right-radius:.25rem}.list-group-item:last-child{margin-bottom:0;border-bottom-right-radius:.25rem;border-bottom-left-radius:.25rem}.list-group-item.disabled,.list-group-item:disabled{color:#6c757d;pointer-events:none;background-color:#fff}.list-group-item.active{z-index:2;color:#fff;background-color:#007bff;border-color:#007bff}.list-group-horizontal{-ms-flex-direction:row;flex-direction:row}.list-group-horizontal .list-group-item{margin-right:-1px;margin-bottom:0}.list-group-horizontal .list-group-item:first-child{border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;border-top-right-radius:0}.list-group-horizontal .list-group-item:last-child{margin-right:0;border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;border-bottom-left-radius:0}@media (min-width:576px){.list-group-horizontal-sm{-ms-flex-direction:row;flex-direction:row}.list-group-horizontal-sm .list-group-item{margin-right:-1px;margin-bottom:0}.list-group-horizontal-sm .list-group-item:first-child{border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;border-top-right-radius:0}.list-group-horizontal-sm .list-group-item:last-child{margin-right:0;border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;border-bottom-left-radius:0}}@media (min-width:768px){.list-group-horizontal-md{-ms-flex-direction:row;flex-direction:row}.list-group-horizontal-md .list-group-item{margin-right:-1px;margin-bottom:0}.list-group-horizontal-md .list-group-item:first-child{border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;border-top-right-radius:0}.list-group-horizontal-md .list-group-item:last-child{margin-right:0;border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;border-bottom-left-radius:0}}@media (min-width:992px){.list-group-horizontal-lg{-ms-flex-direction:row;flex-direction:row}.list-group-horizontal-lg .list-group-item{margin-right:-1px;margin-bottom:0}.list-group-horizontal-lg .list-group-item:first-child{border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;border-top-right-radius:0}.list-group-horizontal-lg .list-group-item:last-child{margin-right:0;border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;border-bottom-left-radius:0}}@media (min-width:1200px){.list-group-horizontal-xl{-ms-flex-direction:row;flex-direction:row}.list-group-horizontal-xl .list-group-item{margin-right:-1px;margin-bottom:0}.list-group-horizontal-xl .list-group-item:first-child{border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;border-top-right-radius:0}.list-group-horizontal-xl .list-group-item:last-child{margin-right:0;border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;border-bottom-left-radius:0}}.list-group-flush .list-group-item{border-right:0;border-left:0;border-radius:0}.list-group-flush .list-group-item:last-child{margin-bottom:-1px}.list-group-flush:first-child .list-group-item:first-child{border-top:0}
		</style>";
	}
 
    
    public static function SendMail($email, $subject, $body, $company, $filename = '') {
            
            
        $from = $company['c_send_from'];
        $body1 = [];
        if (!is_array($email)) $email = [$email];
        
        if (!is_array($body)) { 
            for ($j = 0; $j < count($email); $j++)  $body1[] = $body;
        } else $body1 = $body;
            
        $messages = []; 
        $i = 0;
        foreach ($email as $rowemail) {    
            $message = "<section style='background:{$company['c_primary_color']}; padding:15px;'> <center> <img style='max-height:100px;' src='https://{$_SERVER['SERVER_NAME']}/public/assets/uploads/{$company['c_logo']}'> </center> ";
            $message .= "<div style='background:{$company['c_primary_color']}; position: relative;padding: 0.75rem 1.25rem;margin-bottom: 1rem;border: 1px solid #f3f3f3; border-radius: 0.25rem; color:white'
            ><div style='margin: 2px; padding: 1px; '> $body1[$i]  <hr> <div style='background:{$company['c_primary_color']}; color:white; padding:3px;'> 
            <p>{$company['c_name']} <br>{$company['c_address']}<br> {$company['c_email']} <br> {$_SERVER['SERVER_NAME']}</p> </div> </div></div>";
             
    	    $message .= "<br><hr><div style='background:{$company['c_primary_color']};color:lightgrey;padding:2px;'>This email was sent to $rowemail.The information in this message is confidential and is intended solely for the addressee. 
    	    Access to this e-mail by anyone else is unauthorised. If you are not the intended recipient, any disclosure, copying, distribution or any action taken or omitted in 
    	    reliance on this, is prohibited and may be unlawful. Whilst all reasonable steps are taken to ensure the accuracy and integrity of information and data transmitted 
    	    electronically and to preserve the confidentiality thereof, no liability or responsibility whatsoever is accepted if information or data is, for whatever reason, 
    	    corrupted or does not reach its intended destination. </div>";
    	    $message .= "</section>";
    	    
    	    $messages[] = $message;
    	    $i++;
        }
            
            
        
         require getenv('DOCUMENT_ROOT') . '/libraries/phpmailer/sendemail.php';
        
         return 'success'; 
       
      
        
        $headers = "From:{$company['c_name']} <{$company['c_send_from']}> \r\n";
        $headers .= "Reply-To: <{$company['c_email']}> \r\n";
        $headers .= "Content-type: text/html; utf-8 \r\n";
        
        return (mail($email, $subject, $message, $headers));          
    }
     
    public static function validUrl($url) {
        $url = trim($url);
        $url = strtolower($url);
        $url = str_replace(' ', '-', $url);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $url);
    }
    public static function NoSpaceUrl($url) {
        $url = trim($url);
        $url = strtolower($url);
        $url = str_replace('-', '', $url);
        $url = str_replace(' ', '', $url);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $url);
    }
    public static function NoSpaceUrl1($url) {
        $url = trim($url);
        $url = str_replace('-', '', $url);
        $url = str_replace(' ', '', $url);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $url);
    }
    public static function NoSpaceUrl2($url) {
        $url = trim($url);
        $url = str_replace('-', '', $url);
        $url = str_replace(' ', '', $url);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $url);
    }

    public static function relocate($target, $echo = true) {
        if (!$echo) {
            return "<script> window.location.href='" . $target . "'</script>";
        }
        echo "<script> window.location.href='" . $target . "'</script>";
    }
    /**
     * @param string $name  'from _files variable'
     * @param string $name  'given name for saving'
     */
    public static function movefile($name, $nameAs, $direction = '') {
        $_direction = $direction == '' ? 'uploads' : $direction; 
        return (move_uploaded_file($_FILES[$name]['tmp_name'], getenv("DOCUMENT_ROOT") . "/public/assets/$_direction/" . $nameAs));
                 
    }
    public static function hyperlinks($text, $itemid) {
        $preg = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        //check for urls
        if(preg_match($preg, $text, $url)) {
            return preg_replace($preg, "<a href=/links?id=$itemid&l=".$url[0]." target='_blank'>".$url[0]."</a> ", $text);
        }
        return $text;
        
    }
    
    public static function movefiles($name, $nameAs, int $loop, $direction = '') {    
        $_direction = $direction == '' ? 'uploads' : $direction; 
        return (move_uploaded_file($_FILES[$name]['tmp_name'][$loop], getenv("DOCUMENT_ROOT") . "/public/assets/$_direction/" . $nameAs));
    }

    // get ip address
    public static function getIPAddress() {  
        //whether ip is from the share internet  
         if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
        //whether ip is from the proxy  
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        }  
    //whether ip is from the remote address  
        else {  
            $ip = $_SERVER['REMOTE_ADDR'];  
         }  
         return $ip;  
    } 

    public static function compress_image($source_url, $destination_url = 'public/assets/uploads/', $quality = 60) {
        $info = getimagesize($source_url);
        if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
        else if ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
        else if ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
        imagejpeg($image, $destination_url, $quality);
       
    }
 

    public static function getYears($yrs_back = null) {
        $cur_yr = date('Y');

        if (empty($yrs_back)) $yrs_back = 50;

        $begin_yr = date('Y') - $yrs_back;

        $output = '';
        for ($i = $begin_yr; $i < $cur_yr; $i++) {
            $output .= "<option value='$i'> $i </option>";
        }
        return $output;
    }
   
 
       // this function returns a title cut to fit desired container
    public static function trimTitle($title, $_total = 8) {
        $new_title = explode(" ", $title);
        $total = count($new_title);
        if ($total > $_total) {
            //else return resized title
            $new_title1 = "";
            for ($i = 0; $i < ($_total + 1); $i++) {
                $new_title1 .= $new_title[$i] . ' ';
                // replace space with dots
                if ($i == $_total) {
                    $new_title1 .= $new_title[$i] . '...';
                }
            }
            return $new_title1;
        } else {
            // return the whole value
            return $title;
        }
    }

  
    public static function stringdate($date) {
        $date = date('jS F Y', ($date));
        $explode = explode(' ', $date);
       return $explode = $explode[0] . ' ' . substr($explode['1'], 0, 3) . ', ' . $explode[2]; 
        //return date('jS F Y', strtotime($date));
        
    }
 
    
    
    public static function timeago($date1, $date2 ='') {
        if (empty($date1)) {
            return "1 year ago";
        }
        else if ($date2 == '') {
            $date = $date1;
        } else if (!is_numeric($date2)) {
            $date = strtotime($date2);
        } else {
            $date = $date1;
        }
        
        return CustomFunctions::time_elapsed_string('@' . $date);
    }
      
    public static function time_elapsed_string($datetime, $full = false) {
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
            's' => 'second',
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
    
    public static function maskPhoneNumber($phone, $how_many = 6) {  
        // Remove non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);
        
        if (strlen($phone) < $how_many) {
            return '';//"Invalid phone number";
        }
        
        return substr($phone, 0, $how_many) . str_repeat('*', strlen($phone) - $how_many) . substr($phone, -2);
    }
    
    public static function maskEmail($email, $how_many = 4) {  
        $parts = explode('@', $email);
        
        if (strlen($parts[0]) < $how_many) {
            return '';//"Invalid email address";
        }
        
        return substr($parts[0], 0, $how_many) . str_repeat('*', strlen($parts[0]) - $how_many) . '@' . $parts[1];
    }
      public static function headerExtra($pgTitle, $description, $image, $url, $type, $group, $artistname, $publishdate, $datemodified, $article = 'article') {
        
        $req_uri = "https://www.{$_SERVER['REQUEST_URI']}/{$_SERVER['REQUEST_URI']}";
        $url = str_replace('&', '&amp;', $url); 
        $image_size_array = getimagesize($image );
   
        echo "
            <meta property='author' content='{$_SERVER['SERVER_NAME']}'>
            <meta name='description' content=\"$description\">
            <meta property='og:description' content=\"$description\">
            <meta property='og:image' content='$image'>
            <meta property='og:image:width' content=\"{$image_size_array[0]}\">
            <meta property='og:image:height' content=\"{$image_size_array[1]}\">
            <meta property='og:title' content=\"$pgTitle\">
            <meta property='og:site_name' content='{$_SERVER['SERVER_NAME']}'>
            <meta property='og:url' content='$req_uri'>
            <meta property='article:published_time' content='$publishdate'>
        
            <link rel='canonical'   href='$req_uri'/>
            <!--link rel='alternate' hreflang='x-default'$req_uri'/>
            <link rel='alternate' hreflang='en' href='$req_uri'/>
            <link rel='alternate' hreflang='fr' href='$req_uri'/>-->
        
        
            "; 
            //<script type="application/ld+json" class="rank-math-schema">{"@context":"https://schema.org","@graph":[{"@type":"Organization","@id":"https://thenation.co.za/#organization","name":"The Nation","sameAs":["https://web.facebook.com/thenation24/","https://twitter.com/thenationent"],"logo":{"@type":"ImageObject","@id":"https://thenation.co.za/#logo","url":"https://thenation.co.za/wp-content/uploads/2021/09/20210907_235319.jpg","contentUrl":"https://thenation.co.za/wp-content/uploads/2021/09/20210907_235319.jpg","caption":"The Nation","inLanguage":"en-US","width":"1990","height":"990"}},{"@type":"WebSite","@id":"https://thenation.co.za/#website","url":"https://thenation.co.za","name":"The Nation","publisher":{"@id":"https://thenation.co.za/#organization"},"inLanguage":"en-US"},{"@type":"ImageObject","@id":"https://thenation.co.za/wp-content/uploads/2021/02/20210227_164119.jpg","url":"https://thenation.co.za/wp-content/uploads/2021/02/20210227_164119.jpg","width":"1000","height":"500","caption":"Michelle Botes profile","inLanguage":"en-US"},{"@type":"WebPage","@id":"https://thenation.co.za/bio/michelle-botes-age/#webpage","url":"https://thenation.co.za/bio/michelle-botes-age/","name":"Michelle Botes age, biography, profile, education, husband, recognitions - The Nation","datePublished":"2023-01-25T08:39:56+01:00","dateModified":"2023-01-25T08:39:56+01:00","isPartOf":{"@id":"https://thenation.co.za/#website"},"primaryImageOfPage":{"@id":"https://thenation.co.za/wp-content/uploads/2021/02/20210227_164119.jpg"},"inLanguage":"en-US"},{"@type":"Person","@id":"https://thenation.co.za/bio/michelle-botes-age/#author","name":"Joseph Nkosi","image":{"@type":"ImageObject","@id":"https://secure.gravatar.com/avatar/01684f61d31607aa36bfeebd8fa07b53?s=96&amp;d=mm&amp;r=g","url":"https://secure.gravatar.com/avatar/01684f61d31607aa36bfeebd8fa07b53?s=96&amp;d=mm&amp;r=g","caption":"Joseph Nkosi","inLanguage":"en-US"},"sameAs":["https://thenation.co.za"],"worksFor":{"@id":"https://thenation.co.za/#organization"}},{"@type":"BlogPosting","headline":"Michelle Botes age, biography, profile, education, husband, recognitions - The Nation","keywords":"michelle botes age,michelle botes biography,michelle botes profile,michelle botes husband,michelle botes education","datePublished":"2023-01-25T08:39:56+01:00","dateModified":"2023-01-25T08:39:56+01:00","articleSection":"Biography, South African Celebrities","author":{"@id":"https://thenation.co.za/bio/michelle-botes-age/#author","name":"Joseph Nkosi"},"publisher":{"@id":"https://thenation.co.za/#organization"},"description":"Michelle Botes (born 12 October 1962 in Cape Town) is an award-winning South African actress, language teacher, and aromatherapist. Being able to wear other","name":"Michelle Botes age, biography, profile, education, husband, recognitions - The Nation","@id":"https://thenation.co.za/bio/michelle-botes-age/#richSnippet","isPartOf":{"@id":"https://thenation.co.za/bio/michelle-botes-age/#webpage"},"image":{"@id":"https://thenation.co.za/wp-content/uploads/2021/02/20210227_164119.jpg"},"inLanguage":"en-US","mainEntityOfPage":{"@id":"https://thenation.co.za/bio/michelle-botes-age/#webpage"}}]}</script>


            
            $pgTitle = empty($pgTitle) ?"{$_SERVER['SERVER_NAME']}":$pgTitle; 
       
    }

	 
 
    

    //end of class
    
}


