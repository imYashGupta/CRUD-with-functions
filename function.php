<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Calcutta');
define('host', 'localhost');
define('dbuser', 'root');
define('dbpass', '');
define('dbname', 'test');
define('site_url', $_SERVER['SERVER_NAME']);
define('site_name', 'website.co m' );
define('site_email','example@website.com');

$GLOBALS["conn"]=mysqli_connect(host,dbuser,dbpass,dbname);
ob_start();

 
/**
 * You don,t need to write sqls to inserting a new row into database ,Just use this function for easier syntax
 * 
 * Example to Use:
 * $dataToInsert=["columnName" => "value" , "columnName2" => "value"];
 * insertInto("tblname",$dataToInsert);
 * 
 *
 * @param      string  $tbl_name   Name of Your Table
 * @param      array   $tbl_value  keys and values 
 * @param      string  $datetime   a optional param if have any createdAt column in your table, this will insert datetime!
 *
 * @return     <type>  ( query return true or false )
 */
function insertInto(string $tbl_name,array $tbl_value,string $datetime='')
{   
	 
 	$string='';
	$stringValue='';
	foreach ($tbl_value as $key => $value) {
	 	 $string .=$key.",";
	 } 
	foreach ($tbl_value as $key => $value) {
		 $stringValue .="'".mysqli_real_escape_string($GLOBALS["conn"],$value)."',";
	}
	   $tbl_key= substr($string,0,-1);
	   $tbl_value=substr($stringValue,0,-1);
	   if (!empty($datetime)) {
	     $mysqliQuery=mysqli_query($GLOBALS["conn"],"INSERT INTO $tbl_name($tbl_key,$datetime)VALUES($tbl_value,NOW())");
	   }
	   else{
	   $mysqliQuery=mysqli_query($GLOBALS["conn"],"INSERT INTO $tbl_name($tbl_key)VALUES($tbl_value)");
		}
	   return $mysqliQuery;
}


/**
 * For Fetching single or multiple rows from database
 * 
 * Example to Use:
 * selectFrom("tblname","id='1' AND status='active'");
 *
 * @param      string  $tableName  Name of Your Table
 * @param      string  $condition  conditions
 *
 * @return     <type>  ( query return arrays)
 */
function selectFrom(string $tableName,string $condition=''){
	if (empty($condition)) {
		$fetchQuery=mysqli_query($GLOBALS["conn"],"SELECT * FROM $tableName");
	}
	else{
		$fetchQuery=mysqli_query($GLOBALS["conn"],"SELECT * FROM $tableName WHERE $condition");
	}
	
	return $fetchQuery;
}
/*
	Example to use
	howMany(query);	
*/
function howMany($query){
	$mysqli_num_rows=mysqli_num_rows($query);
	return $mysqli_num_rows;
}
/*
	Example to use
	fetch(query);	
*/
function fetch($query){
	$mysqli_fetch_array=mysqli_fetch_array($query);
	return $mysqli_fetch_array;
}
/* Random string
	Example to use
	$a = token(32);
	$b = token(8, 'abcdefghijklmnopqrstuvwxyz');
*/
function token($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}




/**
 * For Genrating Random string of int or numbers 
 * 
 * Example to Use:
 * tokenInt(8);
 * this will return 8 char long stings i.e 56215465
 *
 * @param      integer  $length    specify the length of strings
 * @param      string   $keyspace  optional param for selected int
 *
 * @return     <type>   ( returns a random numbers strings )
 */
function tokenInt($length, $keyspace = '0123456789')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}
/*
	delete
	Example to use
	deleteRow("tblname","condition");
*/
function deleteRow(string $tblname,string $condition)
{	 
	$deleteQuery=mysqli_query($GLOBALS["conn"],"DELETE FROM $tblname WHERE $condition");
	return $deleteQuery;
}
/**
	 * @param  string $tblname [name of your table]
	 * @param  array  $var_value [name and value for column in table to be update]
	 * @param  string $condition  [such as WHERE clause and AND,OR,NOT]
	 * @param  string $updated_at [optional: if table contain updated at field]
 	 * @return [boolean or string]
 	 *
 	 * Example of use: update("tblname",["field1" => "value","field2" => "value"],"id='1'","updated");
	 */
	function update(string $tblname,array $var_value,string $condition,string $updated_at='')
	{
		$query=mysqli_query($GLOBALS["conn"],"SHOW columns FROM $tblname");
		$strings='';
		$keys='';
		$error='';
		foreach ($var_value as $key => $value) {
			$strings .= $key."='".mysqli_real_escape_string($GLOBALS["conn"],$value)."',";
			$keys .=$key.",";
		}
		$queryBuild=substr($strings,0,-1);
		if (empty($updated_at)) {
			$updateQuery=mysqli_query($GLOBALS["conn"],"UPDATE $tblname SET $queryBuild WHERE $condition");
		}
		else{
			$arr='';
 			 while($row = mysqli_fetch_array($query)){
     			$arr .=$row['Field'].",";
     		
			}
			$keyArray=explode(',',$keys);
			$array=explode(',',$arr );
			foreach ($keyArray as $key) {
		 		if (array_search($key,$array)) {
		 			//echo $results ='1';
		 		}
		 		else{
		 			 $error .=$key.",";
		 		}
		 	}
		 	$errorStrings= substr($error,0,-1);
 			$errorArray=explode(',',$error);
 			$errorCount=count($errorArray);
 			$totalError=$errorCount-1;
 			if ($totalError <= 0) {
 				if (array_search($updated_at,$array)) {
					$updateQuery=mysqli_query($GLOBALS["conn"],"UPDATE $tblname SET $queryBuild,$updated_at=NOW() WHERE $condition");
				}
				else{
					echo $updateQuery="<b>Error!</b> There is no Field in Table <b>".$tblname."</b> with <b>".$updated_at."</b> column.";
				}
 				
 			}
 			else{
 				echo $updateQuery="<b>".$totalError." Error!</b> There is No Field With These column names(".$errorStrings.") in table <b>Users</b> <em>Please Check..</em> ";
 			}
	
			
		}
		
		return $updateQuery;
	}

	/**
	 * { For Redirection }
	 *
	 * @param      string   $path  link Or Path of the file where you want redirection
	 *
	 * @return     boolean  ( this will return js function window.location to redirect )
	 */
	function move(string $path)
	{
		//header("Location:$path");
		echo "<script>window.location.href='$path';</script>";
		return true;
	}
	function secureAdmin()
	{
		if (!isset($_SESSION["admin"]) AND empty($_SESSION["admin"])) {
			 move("https://".site_url);
		}
		return true;
	}
/*
	if You call this method this will check about `user` Session And redirection to Index.php if no pere is give 
	or if you want custom redirection give one single perameter as page url!
*/
		function secureUser($link='')
	{
		if (!isset($_SESSION["user"]) AND empty($_SESSION["user"])) {
			if ($link!="") {
				move("https://".site_url."/".$link);
			}
			else{
				move("https://".site_url);
			}
			 
		}
		return true;
	}



	/**
	 * Gets the user ip.
	 *
	 * @return     <type>  The user ip.
	 */
	function getUserIP(){
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];
	    if(filter_var($client, FILTER_VALIDATE_IP))
	    {
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP))
	    {
	        $ip = $forward;
	    }
	    else
	    {
	        $ip = $remote;
	    }
	    return $ip;
		}


		/**
		 * { this will convert datetime into more readable format i.e 5 minutes ago , a week ago etc. }
		 *
		 * @param      <type>  $timestamp  2018-09-15 22:54:48
		 *
		 * @return     string  
		 */
		function timeAgo($timestamp){  
	      $time_ago = strtotime($timestamp);  
	      $current_time = time();  
	      $time_difference = $current_time - $time_ago;  
	      $seconds = $time_difference;  
	      $minutes      = round($seconds / 60 );           // value 60 is seconds  
	      $hours           = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec  
	      $days          = round($seconds / 86400);          //86400 = 24 * 60 * 60;  
	      $weeks          = round($seconds / 604800);          // 7*24*60*60;  
	      $months          = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60  
	      $years          = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60  
	      if($seconds <= 60)  
	      {  
	     return "Just Now";  
	   }  
	      else if($minutes <=60)  
	      {  
	     if($minutes==1)  
	           {  
	       return "one minute ago";  
	     }  
	     else  
	           {  
	       return "$minutes minutes ago";  
	     }  
	   }  
	      else if($hours <=24)  
	      {  
	     if($hours==1)  
	           {  
	       return "an hour ago";  
	     }  
	           else  
	           {  
	       return "$hours hrs ago";  
	     }  
	   }  
	      else if($days <= 7)  
	      {  
	     if($days==1)  
	           {  
	       return "yesterday";  
	     }  
	           else  
	           {  
	       return "$days days ago";  
	     }  
	   }  
	      else if($weeks <= 4.3) //4.3 == 52/12  
	      {  
	     if($weeks==1)  
	           {  
	       return "a week ago";  
	     }  
	           else  
	           {  
	       return "$weeks weeks ago";  
	     }  
	   }  
	       else if($months <=12)  
	      {  
	     if($months==1)  
	           {  
	       return "a month ago";  
	     }  
	           else  
	           {  
	       return "$months months ago";  
	     }  
	   }  
	      else  
	      {  
	     if($years==1)  
	           {  
	       return "one year ago";  
	     }  
	           else  
	           {  
	       return "$years years ago";  
	     }  
	   }  
	 }


	/**
	 * { to format datetime or timestamp, this will format timestamp like 2018-09-15 22:54:48 into this Sep 15,2018 }
	 *
	 * @param      string  $datetime  timestamp or datetime i.e 2018-09-15 22:54:48
	 * @param      string  $format    datetime format i.e y,m,d,h,s etc
	 *
	 * @return     <type>  ( returns a read able format of timestamps )
	 */
	function dateFormat(string $datetime,string $format)
	 {
	 	$date=date_create($datetime);
		return date_format($date,$format);
	 }

	/**
	 * { This function is work exact like mysqli_real_escape_string() instead of Writing long strings simply use realEscape() }
	 *
	 * @param      string  $str  The string to be escaped. 
	 *
	 * @return     <type>  Returns an escaped string.
	 */
	function realEscape(string $str){
		 
		return mysqli_real_escape_string($GLOBALS["conn"],$str);
	} 


	function post(string $str)
	{
		 
		$index=mysqli_real_escape_string($GLOBALS["conn"],$str);
		return $_POST[$index];
	}
	function lastID(string $tblName)
	{
		 
		$select=mysqli_query($GLOBALS["conn"]," SELECT * FROM $tblName ORDER BY id DESC LIMIT 1 ");
		$row=mysqli_fetch_array($select);
		return $row["id"];
	}
	function query(string $str)
	{
		return mysqli_query($GLOBALS["conn"],$str);
	}
	function response(array $array)
	{
		return exit(json_encode(["response" => $array]));
	}
	function returnJson(int $int,string $str)
	{
		return exit(json_encode(["response" => ["code" => $int ,"msg" => $str]]));
	}
 
?>
