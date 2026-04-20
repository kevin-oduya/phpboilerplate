<?php
#[AllowDynamicProperties]
class Model extends Database {

    // this is the main model,, all others are sub
    // main function is to pass the database connection

    function __construct()
    {
        // $this->connection = new Database();
        // $this->db = new Database();

    }
   
    
 
    /**
	 * @return string - cl could give last return ids when needed
	 */
    protected function _ms($error = false, string $ms = '', string $third = '' ) {
		$newms = $error == false ? "Success" : "An errorr occurred";
		
		return json_encode(array(
			"error"=> $error == false ? "false" : "true",
			"msg"=> empty($ms) ? $newms : $ms,
			"cl"=> $third
		));
		
	}  
	/**
	 * @return array 0=rowcount, 1=data
	 */
	protected function _get(string $table, string $where = '', array $values = [], bool $fetchall = true, string $orderby = '', string $del_rule = '' ): array {
		$substr = substr($where, 0, 1);
		if ($substr == '(') { // the first char is a (
			$where = $where;
		} else $where = $this->_where($where, 'and', '', $del_rule);

		$countvalues = count($values);
		$sql = $countvalues == 0 ? "SELECT * FROM $table $orderby" : "SELECT * FROM $table WHERE $where $orderby";		
		//echo $sql; //echo '<br>'; //die;
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute( $values );
        return $fetchall ? [$stmt->rowCount(), $stmt->fetchAll()] : [$stmt->rowCount(), $stmt->fetch()];
	}
	/**
	 * general query function, not used in the current version but can be used for complex queries that do not fit the other functions
	 */
	protected function _query() {
		$args = func_get_args();
		$sql = array_shift($args);
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute( $args );
		return [$stmt->rowCount(), $stmt->fetchAll()];
	}
    /**
     * @return string value of action ie sum of columns
     */
	protected function _getmore(string $table, string $action, string $where = '', array $values = [] ):string {
		
		$sql = empty($where) ? "SELECT $action as x1 FROM $table" : "SELECT $action as x1 FROM $table WHERE {$this->_where($where)} ";		
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute( $values );
		return $stmt->fetch()['x1'] ?? '0';
	}
	
	/**
	 * @return string json_encoded string
	 */
	protected function _insert(string $table, string $columns = '', array $values = [] ):string {
		$ignore = '';
	    $exp = explode(':', $table);
	    if (isset($exp[1])) $ignore = $exp[1];
	    $table = $exp[0];
	    
		$pdo = $this->connection();
		$sql = "INSERT $ignore INTO $table ($columns) VALUES ({$this->_where($columns, ',', '?')}) ";		
		//echo $sql;die;
		$stmt = $pdo->prepare($sql);
		$eq = ($stmt->execute( $values ));
		$lastId = $pdo->lastInsertId();
		return $this->_ms($eq ? false : true, '', $lastId);		
	}
    
    /**
     * @return string js-encoded feedback message
     */
	protected function _update(string $table, string $columns = '', string $where = '', array $values = [] ):string {
		
		$sql = "UPDATE $table SET {$this->_where($columns, ',')} WHERE {$this->_where($where)}";	
		$stmt = $this->connection()->prepare($sql);
		$eq = ($stmt->execute( $values ));
		return $this->_ms($eq ? false : true);	
	}

    /**
     * @return string js-encoded feedback message
     */
	protected function _delete(string $table, string $where, $values = []):string {
	
		$sql = "DELETE FROM $table WHERE {$this->_where($where)}";
		$stmt = $this->connection()->prepare($sql);
		$eq = ($stmt->execute($values ));
		return $this->_ms($eq ? false : true);
	}
	/**
	 * @param string $action  either create, delete or drop
     * 
     * @return mixed js-encoded feedback message or bool   
	 */
	protected function _tables($tablename, $action = 'create') {
		if ($action == 'delete') {
			$sql = "DELETE FROM $tablename";
			$stmt = $this->connection()->prepare($sql);
			$eq = ($stmt->execute([ ]));
			return $this->_ms($eq);
		} else if ($action == 'drop') {			
			$sql = "DROP TABLE IF EXISTS $tablename";
			$stmt = $this->connection()->prepare($sql);
			$eq = ($stmt->execute([ ]));
			return $this->_ms($eq);
		} else if ($action == 'create') {
			$sql = "CREATE TABLE $tablename (id int primary key auto_increment, val1 int, val2 varchar(20), val3 varchar(20), val4 varchar(20), val5 varchar(20) ) ";
			$stmt = $this->connection()->prepare($sql);
			if ($stmt->execute([ ])) return true;
			else return false;
			
		} else if ($action == 'complete_sql') {
			$sql = "$tablename";
			$stmt = $this->connection()->prepare($sql);
			if ($stmt->execute([ ])) return true;
			else return false;
		}
		return false;
	}

	private function _where($where, $del = 'and', $placeholders = '', $delrule = '') {
		$exp = explode(',', $where);

		
		if (!empty($delrule)) {
			$delall = explode(',', $delrule);
		}
		$where1 = '';
		$i = 0;
		$j = count($exp);
		foreach ($exp as $ex ) {

			if ( ($j - 1) != $i )
				if (!empty($delrule)) {
					$del = $delall[$i];
				}
			$_e1 = explode(' ', trim($ex));
			if (isset($_e1[1]) && (!empty($_e1[1])) ) {
				$ex = $_e1[0];
				$s = $_e1[1];
			} else $s = '=';

			if (!empty( $placeholders)) {
				if ( ($j - 1) == $i ) $where1 .= '? ';
				else $where1 .= "?, ";
			} else {
				if ( ($j - 1) == $i ) $where1 .= $ex . " $s ? ";
				else $where1 .= $ex . " $s ? $del ";
			}
			$i++;
		}
		return $where1;
	}
	
	/**
	 * @return int user_ID
	 * @param string user_url
	 */
	protected function _getId(string $url) {		
		return $this->_get('users', 'user_url',[$url], false  )[1]['user_ID'];
	}
    
	protected function _uniq_url($table, $columnurl) {

		for ($i = 0; $i < 10000000000; $i++) {
			$url = rand(101, 1009) . time();
			if ($this->_get($table, $columnurl, [$url], true )[0] == 0) {
				return $url;
				break;
			}
		}
	}
	protected function _uniq_p_url($names) {
		$url = $names;
		for ($i = 0; $i < 10000000000; $i++) {
			if ($i > 0) $url .= '-' . rand();
			$url = rand(101, 100009) . time(); 
			if ($this->_get('users', 'user_url', [$url], true )[0] == 0) {
				return $url;
				break;
			}
		}
	}

    protected function _gettables() {
		$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='{$this->dbName}'  ";
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute([]);
		return $stmt->fetchAll();
	}
	protected function _columns($table) {
		return $this->_get($table)[1];
	}
	public function _company() { 
		return $this->_get('company  ', '',[], false)[1];
	}
	public function me($id_email = '') { 
	    
	    if (empty($id_email)) {
	        if( Session::get('userid') == null ) return [];
		    return $this->_get('users  ', 'user_ID', [ Session::get('userid') ], false )[1];
	    }
		return $this->_get('users  ', 'user_ID, user_email', [ $id_email, $id_email ], false, '', 'or' )[1];
	} 
    /**
     * @param string message required
     * @return void
     */
    protected function log(String $message, String $type = 'Other') {  
        $this->_insert('logs', 'l_message, l_by, l_type, l_date', [$message, Session::get('userid'), $type, time() ]);
    }
    public function categs() {
       return $this->_get('blog_categories', '',[])[1]; 
    }
  
    public function _content() {
        $content = $this->_get('contents ', '',[], true )[1];
        $output = [];
        foreach ($content as $row) {
            $body = str_replace('[c_name]', $this->_company()['c_name'], $row['cont_body']);
           $output[$row['cont_given_id']] = ['body'=> $body, 'edit'=>$row['cont_body'], 
           'title'=>$row['cont_title'], 'image_name'=>$row['cont_img']  
           ];
        } 
        return $output;
    }
    protected function slug_unique() {
        $ur = CustomFunctions::randchars(3);
        for($i = 3;  $i < 500; $i++) {
            $ur = CustomFunctions::randchars($i);
            if ( $this->_get('blog', 'blog_slug ', [$ur])[0] == 0 ) break; 
        }
        
        return $ur;
    }
	
    protected function _unique_url($table, $col) {
        $ur = '';
        for($i = 2;  $i < 100; $i++) {
            $ur = CustomFunctions::randchars($i);
            if ( $this->_get($table, $col, [$ur])[0] == 0 ) break; 
        }
        
        return $ur;
    }
	
   private function checks_for_blog_url($slug, $table = 'blog', $col = 'blog_slug' ) {
	return ( $this->_get($table, $col, [$slug] )[0] > 0) ? true : false ;
   }
    protected function generate_clean_slug($title, $table = 'blog', $col = 'blog_slug') {
        // Step 1: Convert title to a basic slug
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Remove special chars
        $slug = preg_replace('/[\s-]+/', '-', $slug);       // Replace spaces and multiple hyphens with single hyphen
        $slug = trim($slug, '-');                           // Trim trailing hyphens
    
        // Step 2: Limit to 40 characters max (cleanly)
        $slug = substr($slug, 0, 45);
        $slug = rtrim($slug, '-'); // Avoid trailing hyphen after cutting
    
        // Step 3: Ensure uniqueness
        $original_slug = $slug;
        $i = 1;
        while ($this->checks_for_blog_url($slug, $table, $col )) {
            // Append a number until unique; re-trim if needed
            $suffix = '-' . $i;
            $slug = substr($original_slug, 0, 45 - strlen($suffix)) . $suffix;
            $i++;
        }
    
        return $slug;
    }
    protected function manage_tags($postid = 0 ) {
        $tags = explode(',',$_POST['tags']);  
        
        foreach($tags as $tag) { 
            $tag_data = $this->_get('tags', 'name', [$tag], 0);
            
            if ($tag_data[0] == 0 ) $this->_insert('tags', 'name', [$tag] );  
            
            $tag_data = $this->_get('tags', 'name', [$tag], 0)[1]; 
            
            $this->_insert('post_tags', 'post_id, tag_id', [$postid, $tag_data['tag_id']]);
        }
    }
    
    protected function pagination() {
        $number = (isset($_GET['pg']) && is_numeric($_GET['pg'])) ? $_GET['pg'] : 1; 
		$thisPageFirstResult = ($number - 1) * $this->_company()['user_loop_sequence'];
		
		return " limit $thisPageFirstResult,{$this->_company()['user_loop_sequence']} ";
    }
  

    public function regenerate_xml() {
        
        $output = $this->_defoutput1();
        ini_set('memory_limit','2048M');
        
	set_time_limit(10080); 
        //////////////////// songs //////////////////
        
		
		
		$blog = $this->_get('blog', 'blog_status', ['Active'] )[1];
    	foreach ($blog as $row) { 
    	   
            $links =  '<url>
              <loc>https://'.$_SERVER['HTTP_HOST'].'/blog/view/'.$row['blog_url_for_browser'].'</loc>
              <lastmod>'.date('Y-m-d', $row['blog_date']) .'T'. date('H:i:s', $row['blog_date']) .'+00:00'.'</lastmod>
              <priority>0.75</priority>
            </url>
    	    ';
    	    $output .= $links;
		} 
  
         $output .= "</urlset>";  
        $continue = false;
        $sitemap_file = getenv('DOCUMENT_ROOT') . '/sitemap.xml'; 
        
        if (file_exists($sitemap_file)) {
            if (unlink($sitemap_file)) {  
                $continue = true;
            } else $return = "file not deleted";
        } else $continue = true;
        
        
        
        if ($continue) {
            $s = "Previous file deleted. \r\n"; 
            $action = fwrite( fopen($sitemap_file, 'w'), $output); 
            
            if ($action == false ) $return = $s . "Error occurred in writing";
            else $return = $s . " $action [1] of data written successfully";
        } else $return = "Unknown error";
          
          
          if (isset($_POST['ajax'])) die( $return );
          
          return $return;
        
    }
        protected function _defoutput1() {
        
        return '<?xml version="1.0" encoding="UTF-8"?>
            <urlset
                  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
            <!-- Sitemap autogenerated from https://'.$_SERVER['HTTP_HOST'].' -->
            
            <url>
              <loc>https://'.$_SERVER['HTTP_HOST'].'</loc>
              <lastmod>2023-12-01T17:20:52+00:00</lastmod>
              <priority>1.00</priority>
            </url>  
            <url>
              <loc>https://'.$_SERVER['HTTP_HOST'].'/about</loc>
              <lastmod>2023-12-01T17:20:52+00:00</lastmod>
              <priority>0.80</priority>
            </url>
            <url>
              <loc>https://'.$_SERVER['HTTP_HOST'].'/blog</loc>
              <lastmod>2023-12-01T17:20:52+00:00</lastmod>
              <priority>0.80</priority>
            </url>
            <url>
              <loc>https://'.$_SERVER['HTTP_HOST'].'/contact-us</loc>
              <lastmod>2023-12-13T17:20:52+00:00</lastmod>
              <priority>0.80</priority>
            </url>  
            <url>
              <loc>https://'.$_SERVER['HTTP_HOST'].'/industries</loc>
              <lastmod>2023-12-13T17:20:52+00:00</lastmod>
              <priority>0.80</priority>
            </url>  
            <url>
              <loc>https://'.$_SERVER['HTTP_HOST'].'/examples</loc>
              <lastmod>2023-12-13T17:20:52+00:00</lastmod>
              <priority>0.80</priority>
            </url>  
             
           
           
        ';
    }


	/////////////////////
}
