<?php
class Config {
	private static $instance;
	private static $_perPage = 18;
	public static $g_con;
	public static $pdo;
	public static $htmlpurifier;
	public static $_url = array();

	public static $_PAGE_URL = 'http://localhost/panelbugged/bluebugged/';
	public static $_LEADER_RANK = 6;
	public static $jobs = array();
	public static $_vehicles = array();
	public static $_vehColors = array();
	public static $EMAIL = 'pericolrpgytb@gmail.com';
	public static $_DOMAIN = 'localhost';
	public static $_API = 'demo';
	public static $_DONATION = 5;

	private function __construct() {
		$db['mysql'] = array(
			'host' 		=> 	'localhost',
			'username' 	=> 	'root',
			'password' 	=> 	'',
			'dbname' 	=> 	'ezcode'
		);

		try {
			self::$g_con = new PDO('mysql:host='.$db['mysql']['host'].';dbname='.$db['mysql']['dbname'].';charset=utf8',$db['mysql']['username'],$db['mysql']['password']);
			self::$g_con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			self::$g_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			@file_put_contents('error_log',@file_get_contents('error_log') . $e->getMessage() . "\n");
			die('Something went wrong.If you are the administrator,check error_log.');
		}
		self::_getUrl();
		self::arrays();
	}
	
	public static function init()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public static function limit() {
		if(!isset($_GET['pg']))
			$_GET['pg'] = 1;
		return "LIMIT ".(($_GET['pg'] * self::$_perPage) - self::$_perPage).",".self::$_perPage;
	}
	
	private static function _getUrl() {
		$url = isset($_GET['page']) ? $_GET['page'] : null;
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        self::$_url = explode('/', $url);
	}

	public static function getContent()
	{
		require_once 'assets/vendor/library/HTMLPurifier.auto.php';
		include_once 'inc/header.easycode.php';
		if (self::$_url[0] === 'signature') {
			include 'inc/pages/' . self::$_url[0] . '.easycode.php';
			return;
		}
		if (isset(self::$_url[0]) && !strlen(self::$_url[0]))
			include_once 'inc/pages/index.easycode.php';
		else if (file_exists('inc/pages/' . self::$_url[0] . '.easycode.php'))
		include 'inc/pages/' . self::$_url[0] . '.easycode.php';
		else
			include_once 'inc/pages/index.easycode.php';
		include_once 'inc/footer.easycode.php';
	}
	public static $mysqli;

	public static function timestamp($timestamp) {
		$datetimeFormat = 'd.m.Y H:i';

		$date = new \DateTime();
		$date->setTimestamp($timestamp);
		return $date->format($datetimeFormat);
	}
	public static function get_rows($table,$params,$value) {
		if(is_array($table)) {
			$rows = 0;
			foreach($table as $val) {
				$q = self::$g_con->prepare("SELECT * FROM `".$val."` WHERE `".$params."` = '".$value."'");
				$q->execute();
				$rows += $q->rowCount();
			}
			return $rows;
		}
		$q = self::$g_con->prepare("SELECT * FROM `".$table."` WHERE `".$params."` = '".$value."'");
		$q->execute();
		return $q->rowCount();
	}
	public static function rows($table,$id = 'id',$data = array()) {

		if(is_array($table)) {
			$rows = 0;
			foreach($table as $val) {
				$q = self::$g_con->prepare("SELECT `".$id."` FROM `".$val."`");
				$q->execute();
				$rows += $q->rowCount();
			}
			return $rows;
		}
		if(!empty($data))
			$q = self::$g_con->prepare("SELECT `".$id."` FROM `".$table."` ".key($data)." `".key($data[key($data)])."` = '".$data[key($data)][key($data[key($data)])]."'");
		else
			$q = self::$g_con->prepare("SELECT `".$id."` FROM `".$table."`");
		$q->execute();
		return $q->rowCount();
	}
	
	public static function rows_s($table,$id = 'ID',$term,$var) {

		if(is_array($table)) {
			$rows = 0;
			foreach($table as $val) {
				$q = self::$g_con->prepare("SELECT `".$id."` FROM `".$val."`");
				$q->execute();
				$rows += $q->rowCount();
			}
			return $rows;
		}
		$q = self::$g_con->prepare("SELECT `".$id."` FROM `".$table."` WHERE `".$term."` = ?");
		$q->execute(array($var));
		return $q->rowCount();
	}
	
	public static function xss($data)
	{
	// Fix &entity\n;
	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

	do
	{
	    // Remove really unwanted tags
	    $old_data = $data;
	    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	}
	while ($old_data !== $data);

	// we are done...
	return $data;
	}
	
	public static function clean($text = null) {
		if (strpos($text, 'script') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, 'meta') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, 'document.location') !== false) return '<i><small>Unknown</small></i>';
		if (strpos($text, 'olteanu') !== false) return '<i><small>Unknown</small></i>';
		strtr ($text, array ('olteanuadv' => '<replacement>'));
		$regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
		return preg_replace_callback($regex, function ($matches) {
			return '<a target="_blank" href="'.$matches[0].'">'.$matches[0].'</a>';
		}, $text);
	}
	
	public static function get_table_rows($table) {
		if(is_array($table)) {
			$rows = 0;
			foreach($table as $val) {
				$q = self::$g_con->prepare("SELECT * FROM `".$val."`");
				$q->execute();
				$rows += $q->rowCount();
			}
			return $rows;
		}
		$q = self::$g_con->prepare("SELECT * FROM `".$table."`");
		$q->execute();
		return $q->rowCount();
	}

	public static function getPage() {
		return isset(self::$_url[2]) ? self::$_url[2] : 1;
	}

	public static function isActive($active) {
		if(is_array($active)) {
			foreach($active as $ac) {
				if($ac === self::$_url[0]) return ' class="active"';
			}
			return;
		} else return self::$_url[0] === $active ? ' class="active"' : false;
	}
	
	public static function formatNumber($number) {
		$format = '<font color="green">$</font>'.number_format($number).'';
		return $format;
	}
	
	public static function showSN() {
		if(!isset($_SESSION['staticnotif']) || !strlen($_SESSION['staticnotif'])) $last_notif = '';
		else {
			$last_notif = $_SESSION['staticnotif'];
			$_SESSION['staticnotif'] = '';
			unset($_SESSION['staticnotif']);
		}
		return $last_notif;
	}
	public static function createSN($type, $message) {
		$text = '<div class="alert alert-'.$type.' alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> '.$message.'
		</div>';
		$_SESSION['staticnotif'] = $text;
	}
	public static function csSN($type, $message, $dismiss = true) {
		$text = '<div class="alert alert-'.$type.' alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				'.($dismiss ? '<span aria-hidden="true">×</span>' : '').'
			</button><i class="fa fa-info-circle"></i> '.$message.'
		</div>';
		return $text;
	}
	
	public static function gotoPage($page,$delay = false,$type = false,$message = false) {
		if(strlen($type) > 2) {
			$text = '<div class="alert alert-'.$type.' alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> '.$message.'
			</div>';
			$_SESSION['staticnotif'] = $text;
		}
		if($delay != false && $delay > 0) {
			echo '<meta http-equiv="refresh" content="' . $delay . ';' . self::$_PAGE_URL . $page  . '">';
			return;
		}
		echo '
		<script src="https://github.com/PericolRPG/antiinspect/blob/main/antiprosti.js"></script>';

		header('Location: ' . self::$_PAGE_URL . $page);
	}
	
	public static function timeFuture($time_ago)
	{
		// $time_ago = strtotime($time_ago);
		$cur_time   = time();
		$time_elapsed   = $time_ago - $cur_time;
		$days       = round($time_elapsed / 86400 );

		if($days > -1){
			return "in $days days";
		 }else {
			return "$days days ago";
		}
	}
	public static function timeAgo($time_ago, $icon = true)
	{
		$time_ago = strtotime($time_ago);
		$cur_time   = time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    = $time_elapsed ;
		$minutes    = round($time_elapsed / 60 );
		$hours      = round($time_elapsed / 3600);
		$days       = round($time_elapsed / 86400 );
		$weeks      = round($time_elapsed / 604800);
		$months     = round($time_elapsed / 2600640 );
		$years      = round($time_elapsed / 31207680 );
		// Seconds
		if($seconds <= 60){
			return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." just now";
		}
		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." one minute ago";
			}
			else{
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." $minutes minutes ago";
			}
		}
		//Hours
		else if($hours <=24){
			if($hours==1){
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." an hour ago";
			}else{
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." $hours hours ago";
			}
		}
		//Days
		else if($days <= 7){
			if($days==1){
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." yesterday";
			}else{
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." $days days ago";
			}
		}
		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." a week ago";
			}else{
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." $weeks weeks ago";
			}
		}
		//Months
		else if($months <=12){
			if($months==1){
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." a month ago";
			}else{
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." $months months ago";
			}
		}
		//Years
		else{
			if($years==1){
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." one year ago";
			}else{
				return "".($icon ? "<i class='fa fa-clock-o'></i>" : "")." $years years ago";
			}
		}
	}
	
	public static function isLogged() {
		if(isset($_SESSION['account_panel'])) return 1;
		else return 0;
	}
	
	public static function checkDon() {
		$c = curl_init();
		curl_setopt_array($c, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_URL => 'http://wcode.ro/apitipeee.php?api='.self::$_API.'&donation='.self::$_DONATION.'&web='.self::$_DOMAIN.''
		]);
		$data = curl_exec($c);
		if(strlen($data) > 15) { //DELETE AFTER DEMO
			$data = explode("][",$data);
			for($x = 0; $x < self::$_DONATION; $x++) {
				$result = str_replace(array('[','"',']'), array(''), $data[$x]);
				$row = explode(",",$result);
				for($i = 0; $i < 4; $i++) {
					$w = Config::$g_con->prepare('SELECT `ID` FROM `wcode_donations` WHERE `Username` = ? AND `CreatedAt` = ?');
					$w->execute(array($row[0],$row[3]));
					if(!$w->rowCount()) {
						$wcc = Config::$g_con->prepare('INSERT INTO `wcode_donations` (`Username`,`Message`,`Amount`,`CreatedAt`) VALUES (?,?,?,?)');
						$wcc->execute(array($row[0],$row[1],$row[2],$row[3]));
					}
				}
			}
		}
	}
	
	public static function getName($user, $name = true) {
		if($name == true) {
			$wd = Config::$g_con->prepare('SELECT `name` FROM `users` WHERE `name` = ?');
			$wd->execute(array($user));
			if($wd->rowCount()) {
				$r_data = $wd->fetch();
				$text = $r_data['name'];
			} else $text = $user;
		} else {
			$wd = Config::$g_con->prepare('SELECT `name` FROM `users` WHERE `id` = ?');
			$wd->execute(array($user));
			if($wd->rowCount()) {
				$r_data = $wd->fetch();
				$text = $r_data['name'];
			} else $text = $user;
		}
		return $text;
	}
	
	public static function getID($user) {
		$wd = Config::$g_con->prepare('SELECT `id` FROM `users` WHERE `name` = ?');
		$wd->execute(array($user));
		if($wd->rowCount()) {
			$r_data = $wd->fetch();
			$text = $r_data['id'];
		} else $text = $user;
		return $text;
	}
	
	public static function formatName($user, $name = true) {
		if($name == true) {
			$wd = Config::$g_con->prepare('SELECT `name` FROM `users` WHERE `name` = ?');
			$wd->execute(array($user));
			if($wd->rowCount()) {
				$r_data = $wd->fetch();
				$text = '<a href="'.self::$_PAGE_URL.'profile/'.$r_data['name'].'">'.$r_data['name'].'</a>';
			} else $text = $user;
		} else {
			$wd = Config::$g_con->prepare('SELECT `name` FROM `users` WHERE `id` = ?');
			$wd->execute(array($user));
			if($wd->rowCount()) {
				$r_data = $wd->fetch();
				$text = '<a href="'.self::$_PAGE_URL.'profile/'.$r_data['name'].'">'.$r_data['name'].'</a>';
			} else $text = $user;
		}
		return $text;
	}
	
	public static function showTags($user, $name = true) {
		$text = '';
		if($name == true) {
			$wd = Config::$g_con->prepare('SELECT * FROM `wcode_functions` WHERE `UserName` = ?');
			$wd->execute(array($user));
			if($wd->rowCount()) {
				while($r_data = $wd->fetch(PDO::FETCH_OBJ)) {
					$text = $text . '<span class="label label-primary" style="background-color: '.$r_data->Color.'"> <i class="'.$r_data->Icon.'"></i> '.$r_data->Tag.'</span> ';
				}
			} else $text = '';
		} else {
			$wd = Config::$g_con->prepare('SELECT * FROM `wcode_functions` WHERE `UserID` = ?');
			$wd->execute(array($user));
			if($wd->rowCount()) {
				while($r_data = $wd->fetch(PDO::FETCH_OBJ)) {
					$text = $text . '<span class="label label-primary" style="background-color: '.$r_data->Color.'"> <i class="'.$r_data->Icon.'"></i> '.$r_data->Tag.'</span> ';
				}
			} else $text = '';
		}
		return $text;
	}
	
	public static function insertLog($userid,$username,$log,$vid,$vname) {
		$wn = Config::$g_con->prepare('INSERT INTO `wcode_logs` (`UserID`,`UserName`,`Log`,`VictimID`,`VictimName`) VALUES (?, ?, ?, ?, ?)');
		$wn->execute(array($userid,$username,$log,$vid,$vname));
		return 1;
	}
	
	public static function makeNotification($userid,$username,$notif,$vid,$vname,$link) {
		$wn = Config::$g_con->prepare('INSERT INTO `wcode_notifications` (`UserID`,`UserName`,`Notification`,`FromID`,`FromName`,`Link`) VALUES (?, ?, ?, ?, ?, ?)');
		$wn->execute(array($userid,$username,$notif,$vid,$vname,$link));
		return 1;
	}
	
	public static function isAdmin($user,$admin = 1) {
		if(!isset($_SESSION['account_panel'])) return 0;
		$wc = Config::$g_con->prepare('SELECT `Admin` FROM `users` WHERE `id` = ?');
		$wc->execute(array($user));
		$r_data = $wc->fetch();
		if($r_data['Admin'] >= $admin) return 1;
		else return 0;
	}
	
	public static function getUser() {
		if(isset($_SESSION['account_panel'])) return $_SESSION['account_panel'];
		else return 9999;
	}
	
	public static function getNameFromID($id) {
		$wc = Config::$g_con->prepare('SELECT `name` FROM `users` WHERE `id` = ?');
		$wc->execute(array($id));
		$r_data = $wc->fetch();
		return $r_data['name'];
	}
	
	public static function justFactionName($faction) {
		if($faction != 0) {
			$wc = Config::$g_con->prepare('SELECT `Name` FROM `factions` WHERE `ID` = ?');
			$wc->execute(array($faction));
			$r_data = $wc->fetch();
			return $r_data['Name'];
		} else return 'Civilian';
	}
	public static function factionMembers($faction) {
		$get = Config::$g_con->prepare('SELECT * FROM `users` WHERE `Member` = ?'); $get->execute(array($faction));
		$text = $get->rowCount();
		return $text;
	}
	public static function getEmailFromName($id) {
		$wc = Config::$g_con->prepare('SELECT `Email` FROM `users` WHERE `name` = ?');
		$wc->execute(array($id));
		$r_data = $wc->fetch();
		return $r_data['Email'];
	}
	
	public static function factionName($user,$faction) {
		if($faction != 0) {
			$wc = Config::$g_con->prepare('SELECT `Name` FROM `factions` WHERE `ID` = ?');
			$wc->execute(array($faction));
			$r_data = $wc->fetch();
			
			$wcf = Config::$g_con->prepare('SELECT `Rank` FROM `users` WHERE `name` = ?');
			$wcf->execute(array($user));
			$u_data = $wcf->fetch();
			
			$text = ''.$r_data['Name'].'';
		} else $text = "No faction";
		
		return $text;
	}
	
	public static function create($rows) {
		if(!isset($_GET['pg']))
			$_GET['pg'] = 1;
		$adjacents = "2";
		$prev = $_GET['pg'] - 1;
		$next = $_GET['pg'] + 1;
		$lastpage = ceil($rows/self::$_perPage);
		$lpm1 = $lastpage - 1;

		$pagination = "";
		if($lastpage > 1)
		{
			if($prev != 0)
				$pagination.= "<li class='previous_page'><a href='?pg=1'>« First</a></li>";  
			else 
				$pagination.= "<li class='previous_page disabled'><a>« First</a></li>";  
			if ($lastpage < 7 + ($adjacents * 2))
			{   
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $_GET['pg'])
						$pagination.= "<li class='active'><a href='#'>$counter</a></li>";
					else
						$pagination.= "<li><a href='?pg=$counter'>$counter</a></li>";                   
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))
			{
				if($_GET['pg'] < 1 + ($adjacents * 2))       
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $_GET['pg'])
							$pagination.= "<li class='active'><a href='#'>$counter</a></li>";
						else
							$pagination.= "<li><a href='?pg=$counter'>$counter</a></li>";                   
					}
					$pagination.= "<li class='dots'><a href='#'>...</a></li>";
					$pagination.= "<li><a href='?pg=$lpm1'>$lpm1</a></li>";
					$pagination.= "<li><a href='?pg=$lastpage'>$lastpage</a></li>";       
				}
				elseif($lastpage - ($adjacents * 2) > $_GET['pg'] && $_GET['pg'] > ($adjacents * 2))
				{
					$pagination.= "<li><a href='?pg=1'>1</a></li>";
					$pagination.= "<li><a href='?pg=2'>2</a></li>";
					$pagination.= "<li class='dots'><a href='#'>...</a></li>";
					for ($counter = $_GET['pg'] - $adjacents; $counter <= $_GET['pg'] + $adjacents; $counter++)
					{
						if ($counter == $_GET['pg'])
							$pagination.= "<li class='active'><a href='#'>$counter</a></li>";
						else
							$pagination.= "<li><a href='?pg=$counter'>$counter</a></li>";                   
					}
					$pagination.= "<li class='dots'><a href='#'>...</a></li>";
					$pagination.= "<li><a href='?pg=$lpm1'>$lpm1</a></li>";
					$pagination.= "<li><a href='?pg=$lastpage'>$lastpage</a></li>";      
				}
				else
				{
					$pagination.= "<li><a href='?pg=1'>1</a></li>";
					$pagination.= "<li><a href='?pg=2'>2</a></li>";
					$pagination.= "<li class='dots'><a href='#'>...</a></li>";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $_GET['pg'])
							$pagination.= "<li class='active'><a href='#'>$counter</a></li>";
						else
							$pagination.= "<li><a href='?pg=$counter'>$counter</a></li>";                   
					}
				}
			}
			if($lastpage == (isset($_GET['pg']) ? $_GET['pg'] : 1))
				$pagination.= "<li class='next_page disabled'><a>Last »</a></li>";  
			else 
				$pagination.= "<li class='next_page'><a href='?pg=$lastpage'>Last »</a></li>";  
		}
		$pagination .= "</ul><div class='clearfix'></div>";
		return $pagination;
	}
	
	public static function jobName($user,$job) {
		if($job != 0) {
			$wc = Config::$g_con->prepare('SELECT `jobName` FROM `jobs` WHERE `jobID` = ?');
			$wc->execute(array($job));
			$r_data = $wc->fetch();

			$text = ''.$r_data['jobName'].'';
		} else $text = "No job";
		
		return $text;
	}
	
	public static function getData($table,$data,$id) {
		$wc = Config::$g_con->prepare('SELECT `'.$data.'` FROM `'.$table.'` WHERE `id` = ?');
		$wc->execute(array($id));
		$r_data = $wc->fetch();
		return $r_data[$data];
	}
	public static function getfullData($table,$data,$where,$id) {
		$pp = Config::$g_con->prepare('SELECT `'.$data.'` FROM `'.$table.'` WHERE `'.$where.'` = ?');
		$pp->execute(array($id));
		$r_data = $pp->fetch();
		return $r_data[$data];
	}
	private static function arrays() {
		self::$jobs = array(
			0 => 'Unemployed',
			1 => 'Detective',
			2 => 'Lawyer',
			3 => 'Whore',
			4 => 'Drugs Dealer',
			5 => 'Macelar',
			6 => 'Farmer',
			7 => 'Bus Driver',
			8 => 'Mechanic',
			9 => 'Arms Dealer',
			10 => 'Garbage',
			11 => 'Product Delivery',
			12 => 'Mower Cutter',
			13 => 'Fisherman',
			14 => 'Trucker',
			15 => 'Petrol Delivery Man',
			16 => 'Woodman',
			17 => 'Bale Mover',
			18 => 'Miner',
			19 => 'Transporter Carne'
		);
		self::$_vehicles = array(
			400 => "Landstalker", 401 => "Bravura", 402 => "Buffalo", 403 => "Linerunner", 404 => "Perrenial", 405 => "Sentinel", 406 => "Dumper", 407 => "Firetruck",
			408 => "Trashmaster", 409 => "Stretch", 410 => "Manana", 411 => "Infernus", 412 => "Voodoo", 413 => "Pony", 414 => "Mule", 415 => "Cheetah",
			416 => "Ambulance", 417 => "Leviathan", 418 => "Moonbeam", 419 => "Esperanto", 420 => "Taxi", 421 => "Washington", 422 => "Bobcat", 423 => "Whoopee",
			424 => "BFInjection", 425 => "Hunter", 426 => "Premier", 427 => "Enforcer", 428 => "Securicar", 429 => "Banshee", 430 => "Predator", 431 => "Bus",
			432 => "Rhino", 433 => "Barracks", 434 => "Hotknife", 435 => "Trailer", 436 => "Previon", 437 => "Coach", 438 => "Cabbie", 439 => "Stallion",
			440 => "Rumpo", 441 => "RCBandit", 442 => "Romero", 443 => "Packer", 444 => "Monster", 445 => "Admiral", 446 => "Squalo", 447 => "Seasparrow",
			448 => "Pizzaboy", 449 => "Tram", 450 => "Trailer", 451 => "Turismo", 452 => "Speeder", 453 => "Reefer", 454 => "Tropic", 455 => "Flatbed", 456 => "Yankee",
			457 => "Caddy", 458 => "Solair", 459 => "Berkley\'sRCVan", 460 => "Skimmer", 461 => "PCJ-600", 462 => "Faggio", 463 => "Freeway", 464 => "RCBaron",
			465 => "RCRaider", 466 => "Glendale", 467 => "Oceanic", 468 => "Sanchez", 469 => "Sparrow", 470 => "Patriot", 471 => "Quad", 472 => "Coastguard",
			473 => "Dinghy", 474 => "Hermes", 475 => "Sabre", 476 => "Rustler", 477 => "ZR-350", 478 => "Walton", 479 => "Regina", 480 => "Comet", 481 => "BMX",
			482 => "Burrito", 483 => "Camper", 484 => "Marquis", 485 => "Baggage", 486 => "Dozer", 487 => "Maverick", 488 => "NewsChopper", 489 => "Rancher",
			490 => "FBIRancher", 491 => "Virgo", 492 => "Greenwood", 493 => "Jetmax", 494 => "Hotring", 495 => "Sandking", 496 => "BlistaCompact",
			497 => "PoliceMaverick", 498 => "Boxville", 499 => "Benson", 500 => "Mesa", 501 => "RCGoblin", 502 => "HotringRacerA", 503 => "HotringRacerB",
			504 => "BloodringBanger", 505 => "Rancher", 506 => "SuperGT", 507 => "Elegant", 508 => "Journey", 509 => "Bike", 510 => "MountainBike",	511 => "Beagle",
			512 => "Cropduster", 513 => "Stunt", 514 => "Tanker", 515 => "Roadtrain", 516 => "Nebula", 517 => "Majestic", 518 => "Buccaneer", 519 => "Shamal",
			520 => "Hydra", 521 => "FCR-900", 522 => "NRG-500", 523 => "HPV1000", 524 => "CementTruck", 525 => "TowTruck", 526 => "Fortune", 527 => "Cadrona",
			528 => "FBITruck",529 => "Willard", 530 => "Forklift", 531 => "Tractor", 532 => "Combine", 533 => "Feltzer", 534 => "Remington", 535 => "Slamvan",
			536 => "Blade", 537 => "Freight",538 => "Streak", 539 => "Vortex", 540 => "Vincent", 541 => "Bullet", 542 => "Clover", 543 => "Sadler", 544 => "Firetruck",
			545 => "Hustler", 546 => "Intruder", 547 => "Primo", 548 => "Cargobob", 549 => "Tampa", 550 => "Sunrise", 551 => "Merit", 552 => "Utility", 553 => "Nevada",
			554 => "Yosemite", 555 => "Windsor", 556 => "Monster", 557 => "Monster", 558 => "Uranus", 559 => "Jester", 560 => "Sultan", 561 => "Stratium",
			562 => "Elegy", 563 => "Raindance", 564 => "RCTiger", 565 => "Flash", 566 => "Tahoma", 567 => "Savanna", 568 => "Bandito", 569 => "FreightFlat",
			570 => "StreakCarriage", 571 => "Kart", 572 => "Mower", 573 => "Dune", 574 => "Sweeper", 575 => "Broadway", 576 => "Tornado", 577 => "AT-400",
			578 => "DFT-30", 579 => "Huntley", 580 => "Stafford", 581 => "BF-400", 582 => "NewsVan", 583 => "Tug", 584 => "Trailer", 585 => "Emperor", 586 => "Wayfarer",
			587 => "Euros", 588 => "Hotdog", 589 => "Club", 590 => "FreightBox", 591 => "Trailer", 592 => "Andromada", 593 => "Dodo", 594 => "RCCam", 595 => "Launch",
			596 => "PoliceCar", 597 => "PoliceCar", 598 => "PoliceCar", 599 => "PoliceRanger", 600 => "Picador", 601 => "S.W.A.T", 602 => "Alpha", 603 => "Phoenix",
			604 => "Glendale", 605 => "Sadler", 606 => "Luggage", 607 => "Luggage", 608 => "Stairs", 609 => "Boxville", 610 => "Tiller", 611 => "UtilityTrailer"
		);

		self::$_vehColors = array(
			'#000000', '#F5F5F5', '#2A77A1', '#840410', '#263739', '#86446E', '#D78E10', '#4C75B7', '#BDBEC6', '#5E7072',
			'#46597A', '#656A79', '#5D7E8D', '#58595A', '#D6DAD6', '#9CA1A3', '#335F3F', '#730E1A', '#7B0A2A', '#9F9D94',
			'#3B4E78', '#732E3E', '#691E3B', '#96918C', '#515459', '#3F3E45', '#A5A9A7', '#635C5A', '#3D4A68', '#979592',
			'#421F21', '#5F272B', '#8494AB', '#767B7C', '#646464', '#5A5752', '#252527', '#2D3A35', '#93A396', '#6D7A88',
			'#221918', '#6F675F', '#7C1C2A', '#5F0A15', '#193826', '#5D1B20', '#9D9872', '#7A7560', '#989586', '#ADB0B0',
			'#848988', '#304F45', '#4D6268', '#162248', '#272F4B', '#7D6256', '#9EA4AB', '#9C8D71', '#6D1822', '#4E6881',
			'#9C9C98', '#917347', '#661C26', '#949D9F', '#A4A7A5', '#8E8C46', '#341A1E', '#6A7A8C', '#AAAD8E', '#AB988F',
			'#851F2E', '#6F8297', '#585853', '#9AA790', '#601A23', '#20202C', '#A4A096', '#AA9D84', '#78222B', '#0E316D',
			'#722A3F', '#7B715E', '#741D28', '#1E2E32', '#4D322F', '#7C1B44', '#2E5B20', '#395A83', '#6D2837', '#A7A28F',
			'#AFB1B1', '#364155', '#6D6C6E', '#0F6A89', '#204B6B', '#2B3E57', '#9B9F9D', '#6C8495', '#4D8495', '#AE9B7F',
			'#406C8F', '#1F253B', '#AB9276', '#134573', '#96816C', '#64686A', '#105082', '#A19983', '#385694', '#525661',
			'#7F6956', '#8C929A', '#596E87', '#473532', '#44624F', '#730A27', '#223457', '#640D1B', '#A3ADC6', '#695853',
			'#9B8B80', '#620B1C', '#5B5D5E', '#624428', '#731827', '#1B376D', '#EC6AAE', '#000000',
			'#177517', '#210606', '#125478', '#452A0D', '#571E1E', '#010701', '#25225A', '#2C89AA', '#8A4DBD', '#35963A',
			'#B7B7B7', '#464C8D', '#84888C', '#817867', '#817A26', '#6A506F', '#583E6F', '#8CB972', '#824F78', '#6D276A',
			'#1E1D13', '#1E1306', '#1F2518', '#2C4531', '#1E4C99', '#2E5F43', '#1E9948', '#1E9999', '#999976', '#7C8499',
			'#992E1E', '#2C1E08', '#142407', '#993E4D', '#1E4C99', '#198181', '#1A292A', '#16616F', '#1B6687', '#6C3F99',
			'#481A0E', '#7A7399', '#746D99', '#53387E', '#222407', '#3E190C', '#46210E', '#991E1E', '#8D4C8D', '#805B80',
			'#7B3E7E', '#3C1737', '#733517', '#781818', '#83341A', '#8E2F1C', '#7E3E53', '#7C6D7C', '#020C02', '#072407',
			'#163012', '#16301B', '#642B4F', '#368452', '#999590', '#818D96', '#99991E', '#7F994C', '#839292', '#788222',
			'#2B3C99', '#3A3A0B', '#8A794E', '#0E1F49', '#15371C', '#15273A', '#375775', '#060820', '#071326', '#20394B',
			'#2C5089', '#15426C', '#103250', '#241663', '#692015', '#8C8D94', '#516013', '#090F02', '#8C573A', '#52888E',
			'#995C52', '#99581E', '#993A63', '#998F4E', '#99311E', '#0D1842', '#521E1E', '#42420D', '#4C991E', '#082A1D',
			'#96821D', '#197F19', '#3B141F', '#745217', '#893F8D', '#7E1A6C', '#0B370B', '#27450D', '#071F24', '#784573',
			'#8A653A', '#732617', '#319490', '#56941D', '#59163D', '#1B8A2F', '#38160B', '#041804', '#355D8E', '#2E3F5B',
			'#561A28', '#4E0E27', '#706C67', '#3B3E42', '#2E2D33', '#7B7E7D', '#4A4442', '#28344E'
		);
	}
	
	public static function generateRandomString($length = 10) {

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$charactersLength = strlen($characters);

		$randomString = '';

		for ($i = 0; $i < $length; $i++) {

			$randomString .= $characters[rand(0, $charactersLength - 1)];

		}

		return $randomString;

	}
}
?>