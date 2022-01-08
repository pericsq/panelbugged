<?php
date_default_timezone_set('Europe/Bucharest');
$time = time();

function getUserIpAddr(){
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
$ip = $_SERVER['REMOTE_ADDR'];
}
return $ip;
}
?> 
<?php
if(Config::isLogged()) Config::gotoPage("", 0, "danger", "You are logged!");
?>

<?php if(isset(Config::$_url[1]) && Config::$_url[1] == "recovery" && isset(Config::$_url[2])) { ?>
<div class="container-fluid">
<div class="panel" style="padding: 10px; margin-bottom:5px">

<?php		   
	if(isset($_POST['change_submit'])) {
		$wc = Config::$g_con->prepare('SELECT * FROM `wcode_recover` WHERE `token` = ?');
		$wc->execute(array(Config::$_url[2]));
		if($wc->rowCount()) {	
			$new = $wc->fetch(PDO::FETCH_OBJ);	
			$k = Config::$g_con->prepare('UPDATE `users` SET `password` = ? WHERE `id` = ?');
			$k->execute(array(md5($_POST['password']),$new->user));
			$k = Config::$g_con->prepare('DELETE FROM `wcode_recover` WHERE `user` = ?');
			$k->execute(array($new->user));
			Config::gotoPage("", 0, "success", "Your password has been successfully modified!");
			} else Config::gotoPage("", 0, "danger", "The token has invalid.");
	}
	?>	

	<center>
	<form method="POST">
		<h4>Change password</h4>
		<br>
		<input placeholder="Enter your new password" class="form-control" type="password" name="password" style="width: 100%">
		<br>
		<button type="submit" name="change_submit" class="btn btn-primary btn-block" title="Create"><i class="ti-pencil"></i>Submit password
		</button>
		</form>
	</center>
</div>
<script>
$(function()
{
	$('[data-toggle="popover"]').popover();

});
</script></div>
<?php } ?>