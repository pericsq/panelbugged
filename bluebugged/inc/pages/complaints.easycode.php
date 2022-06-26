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
<div class="panel" style="padding: 10px">
<div class="page-content">
<div class="row-fluid">
<div class="span12">
<?php
if(!Config::isLogged()) Config::gotoPage("", 0, "danger", "You must be logged in to can access the page!");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
if(!isset(Config::$_url[1])) {
echo '
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Complaints
</p>
</div>
</div>
<br>
<a href="'.Config::$_PAGE_URL.'search" class="btn btn-danger">New Complaint</a>
<a href="mycomplaints" class="btn btn-info">Reclamatii create de mine</a>
<br> <br>
';
?>

<table id="sample-table-1" class="table table-striped table-dark table-bordered table-hover table-responsive-sm">
<thead>
<tr>
<th>Title</th>
<th>Created By</th>
<th>
<i class="icon-time bigger-110 hidden-480"></i>
Date
</th>
<th class="hidden-480">Status</th>
</tr>
</thead>
<tbody>
<?php
$wcs = Config::$g_con->prepare('SELECT `ID`,`UserName`,`AccusedName`,`Category`,`Motiv`,`Faction`,`Status`,`Date` FROM `wcode_complaints` WHERE `Faction` = 0 ORDER BY `Date` DESC '.Config::limit().'');
$wcs->execute();
while($compl = $wcs->fetch(PDO::FETCH_OBJ)) {
$Motiv = '<span class="">Other</span>';
if($compl->Motiv == 1) $Motiv = '<span class="">Offensive language</span>';
if($compl->Motiv == 2) $Motiv = '<span class="">DM</span>';
if($compl->Motiv == 3) $Motiv = '<span class="">Scam</span>';
if($compl->Motiv == 4) $Motiv = '<span class="">Admin/helper abuse</span>';
echo '
<tr>
<td>
<a href="'.Config::$_PAGE_URL.'complaints/view/'.$compl->ID.'">'.$compl->AccusedName.' - '.$Motiv.'
</a>
</td>
<td>'.Config::formatName($compl->UserName).'</td>
<td>'.$compl->Date.'</td>
<td>'.(!$compl->Status ? '<span class="label label-success label-transparent">Open</span>' : '<span class="label label-danger label-transparent">Closed</span>').'</td>
</tr>
';
}
?>

</tbody>
</table>
<?php echo Config::create(Config::rows("wcode_complaints","ID"));

} else if(Config::$_url[1] == "view" && isset(Config::$_url[2]) && is_numeric(Config::$_url[2])) {
$wcs = Config::$g_con->prepare('SELECT * FROM `wcode_complaints` WHERE `ID` = ?');
$wcs->execute(array(Config::$_url[2]));
if(!$wcs->rowCount()) Config::gotoPage("complaints", 0, "warning", "Complaint that you wanted to access doesn't exist!");
$c = $wcs->fetch(PDO::FETCH_OBJ);
?>

<?php 
$kennyload = Config::$g_con->prepare('SELECT * FROM `users` WHERE `name` = ?');  
$kennyload->execute(array($c->AccusedName));
$against = $kennyload->fetch(PDO::FETCH_OBJ); ?>  

<?php 
$kennyload = Config::$g_con->prepare('SELECT * FROM `users` WHERE `name` = ?');  
$kennyload->execute(array($c->UserName));
$created = $kennyload->fetch(PDO::FETCH_OBJ); ?>  
<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="row">
<div class="col-lg-3">
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Complaint Against
</p>
</div>
</div>

<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<div>
<?php echo '
<a href="'.Config::formatName($comment->name).'"><img src="https://bluepanel.bugged.ro/img/bigskins/'.Config::getData("users","CChar",$comment->name).'.png"" style="height: 100px; padding-right: 10px" class="float-left" alt="easycode"></a>'; 
?>
</div>
<p class="pull-left">
<?php
$wcjd = Config::$g_con->prepare('SELECT `id`,`name`,`CChar`,`Warns`,`lastOn`,`Level`,`RegisterDate`,`ConnectedTime`,`Rank` FROM `users` WHERE `id` = ?');
$wcjd->execute(array($c->UserID));
$user = $wcjd->fetch(PDO::FETCH_OBJ);
if(!$c->Status)$status = '<span class="badge bg-success text-white">OPENED</span>';
echo '
<b>Nickname</b>: <a>'.Config::formatName($user->name).'</a><br>
<b>Level</b>: '.Config::getData("users","Level",$c->UserID).'<br>
Hours played: '.Config::getData("users","ConnectedTime",$c->UserID).'<br>
Warns: '.Config::getData("users","Warns",$c->UserID).'/3<br>
';
?>
</p>
<span class="clearfix"></span>
<hr>
<h4>Complaint status</h4>
Status: <b><?php if($c->Status) echo '<b>Closed</b>'; else echo '<b>Open</b>';?></b><br>
Admin replies: <b><abbr title="Optiunea aceasta va fi implementata in viitor.">N/A</abbr></b><br>
Views: <b><abbr title="Optiunea aceasta va fi implementata in viitor.">N/A</abbr></b><br>
Creat pe: <b><?php echo $c->Date ?></b><br>
<br>
</div>

</div>
</div>

<?php if(Config::isAdmin(Config::getUser(), 1)) { ?>	
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Admin Actions
</p>
</div>
</div>

<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<div>
<form method="post">	
		<input style="width: 100%;"  class="form-control  text-white" placeholder="Reason" name="areason" type="text">
		<br>
		<input style="width: 100%;"  class="form-control  text-white" placeholder="Time (999 for ban permanent)" name="atime" type="number">
		<br>
		<center>
		<button class="btn btn-primary btn-lg active"  name="aban" type="submit">  Ban</button>
		
		<button class="btn btn-danger btn-lg active" name="awarn" type="submit">  Warn</button>

		<button class="btn btn-info btn-lg active" name="amute" type="submit"> Mute</button>
		<br>
		<br>
		<button class="btn-lg active btn btn-info" name="ajail" type="submit"> Jail</button>
		
		<button class="btn btn-warning btn-lg active" name="adm" type="submit"> Owner >></button>
		</center>
	</form>
</div>
</div>
</div>
</div>

<?php } ?>
</div>	
<div class="col-lg-9">
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Complaint Details
</p>

</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<?php
$wcjd = Config::$g_con->prepare('SELECT `id`,`name`,`CChar`,`Warns`,`lastOn`,`Level`,`RegisterDate`,`ConnectedTime`,`Rank` FROM `users` WHERE `id` = ?');
$wcjd->execute(array($c->AccusedID));
$user = $wcjd->fetch(PDO::FETCH_OBJ);
if(!$c->Status)$status = '<span class="badge bg-success text-white">OPENED</span>';
echo '
<b>Nickname</b>: <a>'.Config::formatName($user->name).'</a><br>
<b>Level</b>: '.Config::getData("users","Level",$c->AccusedID).'<br>
<b>Motiv reclamatie</b>:';
?>
<?php 
$Motiv = '<span class="">Other</span>';
if($c->Motiv == 1) $Motiv = '<span class="">Offensive language</span>';
if($c->Motiv == 2) $Motiv = '<span class="">DM</span>';
if($c->Motiv == 3) $Motiv = '<span class="">Scam</span>';
if($c->Motiv == 4) $Motiv = '<span class="">Admin/helper abuse</span>';
echo '
'.$Motiv.'
'; 
?>
<hr>
<?php echo $c->Text ?>
<br>
<br>
</div>
</div>
</div>
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Comments
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<?php
$wcjd = Config::$g_con->prepare('SELECT * FROM `wcode_commentaries` WHERE `Section` = "complaints" AND `TopicID` = ? ORDER BY `ID` ASC');
$wcjd->execute(array(Config::$_url[2]));
if(!$wcjd->rowCount()) echo '<center><i>There are no comments</i></center>';
else {
while($comment = $wcjd->fetch(PDO::FETCH_OBJ)) {
echo '
<div class="card bg-dark-2 text-white"><div class="card-text">
<div class="body  ">
<a><img src="https://bluepanel.bugged.ro/img/bigskins/'.Config::getData("users","CChar",$comment->UserID).'.png" style="height: 70px; padding-right: 15px; padding-left: 10px" class="float-left" alt="'.Config::getData("users","name",$comment->UserID).'"></a>
<div class="time float-right text-white-secondary">
<i class="material-icons">access_time</i>
<span class="green">
'.Config::timeAgo($comment->Date).'
</span>
</div>
<div class="text" style="padding-left: 50px">
<p>
'.Config::formatName($comment->UserName).'';
if($comment->UserID == $c->UserID) echo ' <span class="badge badge-info">complaint creator</span>' ;
else if($comment->UserID == $c->AccusedID) echo ' <span class="badge badge-warning">reported player</span>';
else if(Config::isAdmin($comment->UserID)) echo ' <i class="material-icons text-warning" title="Premium">local_parking</i>';
else if($c->Faction == Config::getData("users","Member",Config::getUser()) && Config::getData("users","Rank",Config::getUser()) >= 6)  echo ' <span class="badge badge-warning">reported player</span>';
else echo '';
echo '
</span>
<br>
<span class="comment">
'.$comment->Text.'
</span>
<span class="float-right">
</span>
</p>
</div>
</div> 
</div></div> 
<br>

';
}
}
if($c->Status == 0) {
if(isset($_POST['delete_com']) && Config::getData("users","Admin",Config::getUser())) {
$wcjd = Config::$g_con->prepare('DELETE FROM `wcode_commentaries` WHERE `ID` = ?');
$wcjd->execute(array($_POST['delete_com']));
Config::gotoPage('complaints/view/'.Config::$_url[2].'',0,"success","You deleted with success the comment!");
}
if($c->UserID == Config::getUser() || $c->AccusedID == Config::getUser() || Config::getData("users","Admin",Config::getUser())  || ($c->Faction == Config::getData("users","Member",Config::getUser())) && Config::getData("users","Rank",Config::getUser()) >= 6)
{
if(isset($_POST['comment_send']) && strlen($_POST['text_comm'])) {
$wcjd = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
$wcjd->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","CChar",Config::getUser()),$_POST['text_comm'],Config::$_url[2]));
Config::gotoPage('complaints/view/'.Config::$_url[2].'',0,"success","Your comment has been published!");
$notif = 'New comment has been posted in complaint!';
$link = Config::$_PAGE_URL.'complaints/view/' . Config::$_url[2];

if($c->UserID == Config::getUser()) Config::makeNotification($c->AccusedID,$c->AccusedName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
else if($c->AccusedID == Config::getUser()) Config::makeNotification($c->UserID,$c->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
else if(Config::getData("users","Admin",Config::getUser())) {
Config::makeNotification($c->UserID,$c->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
Config::makeNotification($c->AccusedID,$c->AccusedName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
}
else if($c->Faction == Config::getData("users","Member",Config::getUser()) && Config::getData("users","Rank",Config::getUser()) >= 6) {
Config::makeNotification($c->UserID,$c->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
Config::makeNotification($c->AccusedID,$c->AccusedName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
}

}
?>	
<br>
<form method="post">
<div class="form-group">
<label>welcome a Reply</label>
<textarea class="input-block-level form-control" name="text_comm" placeholder=""></textarea>
<br><br>
<input type="submit" name="comment_send" class="btn btn-small btn-primary" value="Post">
</div>
</form>
<?php } else {
	echo '<center><span class="label label-warning label-transparent">ONLY INVOLVED MEMBERS IN THIS COMPLAINT CAN REPLY</span></center>';
}
} else { ?>
<form method="post">
<div class="form-group">
<label>Leave a Reply</label>
<textarea class="input-block-level form-control" placeholder="You can't reply to this topic, reason: Only admins, helpers, faction leaders and the player that created the complaint can reply to complaints for admin/helper abuse." name="text" disabled=""></textarea>
<br><br>
<button class="btn btn-primary" type="submit" disabled>Post</button>
<?php }
	if($c->Status == 1) {
		if(Config::getData("users","Admin",Config::getUser()) || ($c->Faction == Config::getData("users","Member",Config::getUser())) && Config::getData("users","Rank",Config::getUser()) >= 6) {
			if(isset($_POST['open'])) {
				$wcjd = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 0, `ActionBy` = ? WHERE `ID` = ?');
				$wcjd->execute(array(Config::getData("users","Name",Config::getUser()),Config::$_url[2]));
				Config::gotoPage('complaints/view/'.Config::$_url[2].'',0,"success","You opened with success the complaint!");
			}
			echo '
				<button type="submit" class="btn btn-success btn-xs" name="open">Open</b></button>
			';
		}
	}
	if($c->Status == 0) {
		if(Config::getData("users","Admin",Config::getUser()) || ($c->Faction == Config::getData("users","Member",Config::getUser())) && Config::getData("users","Rank",Config::getUser()) >= 6) {
			if(isset($_POST['close'])) {
				$wcjd = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 1, `ActionBy` = ? WHERE `ID` = ?');
				$wcjd->execute(array(Config::getData("users","Name",Config::getUser()),Config::$_url[2]));
				Config::gotoPage('complaints/view/'.Config::$_url[2].'',0,"success","You closed with success the complaint!");
			}
			echo '
				<button type="submit" class="btn btn-danger btn-xs" name="close">Close</b></button>
			';
		}
	}
	?>
	</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<!-- 
Actiuni pentru reclamatii
-->
<?php if($c->Status == 0) {	?>
<?php if($c->Faction == 0) { ?>
<?php if(Config::isAdmin(Config::getUser(), 1)) { ?>

<?php if(isset($_POST['aban'])) {

$areason = htmlspecialchars($_POST['areason']);
$atime = htmlspecialchars($_POST['atime']);	

if(!$_POST['areason'] || !$_POST['atime']) {	
echo '<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> You left fields blank (reason & time).
		</div>';	
} else { 	

if ($atime == 999) { 
$quee = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
$quee->execute(array($against->name, $against->id,Config::getData("users","name",Config::getUser()),Config::getData("users","id",Config::getUser()), 4, $atime, $areason, date('d/m/Y H:i', $time)));

$commentaries = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
$commentaries->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","Model",Config::getUser()),"The player was sanctioned!",Config::$_url[2]));

$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
$sanctions->execute(array(date('d/m/Y H:i', $time), $against->name, Config::getData("users","name",Config::getUser()), $against->id, 0, $areason));

$lock = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 1, `ActionBy` = ? WHERE `ID` = ?');
$lock->execute(array(Config::getData("users","name",Config::getUser()),Config::$_url[2]));
} else { 
$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
$que->execute(array($against->name, $against->id,Config::getData("users","name",Config::getUser()),Config::getData("users","id",Config::getUser()), 0, $atime, $areason, date('d/m/Y H:i', $time)));

$commentaries = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
$commentaries->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","Model",Config::getUser()),"The player was sanctioned!",Config::$_url[2]));

$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
$sanctions->execute(array(date('d/m/Y H:i', $time), $against->name, Config::getData("users","name",Config::getUser()), $against->id, 0, $areason));

$lock = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 1, `ActionBy` = ? WHERE `ID` = ?');
$lock->execute(array(Config::getData("users","name",Config::getUser()),Config::$_url[2]));
}
?>
<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> Complaint closed.
		</div>
		<meta http-equiv = "refresh" content = "1" />
<?php } } ?>

<?php if(isset($_POST['awarn'])) {

$areason = htmlspecialchars($_POST['areason']);
$atime = htmlspecialchars($_POST['atime']);		

if(!$_POST['areason']) {	
echo '<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> You left fields blank (reason).
		</div>';		
} else { 	
if ($against->Warnings == 2) { 
echo '
<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> This player are 2 warnings, please give ban.
		</div>
'; } else {
$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
$que->execute(array($against->name, $against->id,Config::getData("users","name",Config::getUser()),Config::getData("users","id",Config::getUser()), 1, $atime, $areason, date('d/m/Y H:i', $time)));

$commentaries = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
$commentaries->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","Model",Config::getUser()),"The player was sanctioned!",Config::$_url[2]));

$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
$sanctions->execute(array(date('d/m/Y H:i', $time), $against->name, Config::getData("users","name",Config::getUser()), $against->id, 0, $areason));

$lock = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 1, `ActionBy` = ? WHERE `ID` = ?');
$lock->execute(array(Config::getData("users","name",Config::getUser()),Config::$_url[2]));
?>
<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> Complaint closed.
		</div>
<meta http-equiv = "refresh" content = "1" />
<?php } } } ?>


<?php if(isset($_POST['ajail'])) {

$areason = htmlspecialchars($_POST['areason']);
$atime = htmlspecialchars($_POST['atime']);	

if(!$_POST['areason'] || !$_POST['atime']) {	
echo '<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> You left fields blank (reason & time).
		</div>';		
} else { 		
$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
$que->execute(array($against->name, $against->id,Config::getData("users","name",Config::getUser()),Config::getData("users","id",Config::getUser()), 3, $atime, $areason, date('d/m/Y H:i', $time)));

$commentaries = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
$commentaries->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","Model",Config::getUser()),"The player was sanctioned!",Config::$_url[2]));

$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
$sanctions->execute(array(date('d/m/Y H:i', $time), $against->name, Config::getData("users","name",Config::getUser()), $against->id, 0, $areason));

$lock = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 1, `ActionBy` = ? WHERE `ID` = ?');
$lock->execute(array(Config::getData("users","name",Config::getUser()),Config::$_url[2]));
?>
<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> Complaint closed.
		</div>
<meta http-equiv = "refresh" content = "1" />
<?php } } ?>

<?php if(isset($_POST['adm'])) {

$areason = htmlspecialchars($_POST['areason']);
$atime = htmlspecialchars($_POST['atime']);	

if(!$_POST['areason'] || !$_POST['atime']) {	
echo '<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> You left fields blank (reason & time).
		</div>';		
} else { 		
$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
$que->execute(array($against->name, $against->id,Config::getData("users","name",Config::getUser()),Config::getData("users","id",Config::getUser()), 5, $atime, $areason, date('d/m/Y H:i', $time)));

$commentaries = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
$commentaries->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","Model",Config::getUser()),"The player was sanctioned!",Config::$_url[2]));

$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
$sanctions->execute(array(date('d/m/Y H:i', $time), $against->name, Config::getData("users","name",Config::getUser()), $against->id, 0, $areason));

$lock = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 1, `ActionBy` = ? WHERE `ID` = ?');
$lock->execute(array(Config::getData("users","name",Config::getUser()),Config::$_url[2]));
?>
<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> Complaint closed.
		</div>
<meta http-equiv = "refresh" content = "1" />
<?php } } ?>
<?php if(isset($_POST['amute'])) {
$areason = htmlspecialchars($_POST['areason']);
$atime = htmlspecialchars($_POST['atime']);
if(!$_POST['areason'] || !$_POST['atime']) {		
echo '<div class="alert alert-danger alert-dismissible" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">×</span>
</button><i class="fa fa-info-circle"></i> You left fields blank (reason).
</div>';	} else { 	

$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
$que->execute(array($against->name, $against->id,Config::getData("users","name",Config::getUser()),Config::getData("users","id",Config::getUser()), 2, $atime, $areason, date('d/m/Y H:i', $time)));

$commentaries = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
$commentaries->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","Model",Config::getUser()),"The player was sanctioned!",Config::$_url[2]));

$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
$sanctions->execute(array(date('d/m/Y H:i', $time), $against->name, Config::getData("users","name",Config::getUser()), $against->id, 0, $areason));

$lock = Config::$g_con->prepare('UPDATE `wcode_complaints` SET `Status` = 1, `ActionBy` = ? WHERE `ID` = ?');
$lock->execute(array(Config::getData("users","name",Config::getUser()),Config::$_url[2]));
?>
<div class="alert alert-success alert-dismissible" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">×</span>
</button><i class="fa fa-info-circle"></i> Complaint closed.
</div>
<meta http-equiv = "refresh" content = "1" />
<?php } } ?>
<?php } } } ?>
<?php
} 
else if(Config::$_url[1] == "create") {
if(!isset(Config::$_url[2])) {
if(!isset($_POST['search'])) {
?>
<form method="post">
<center><div class="input-group" style="width: 38%;">
<input class="form-control" type="text" placeholder="Search player name" name="user">
<span class="input-group-btn">
<button class="btn btn-primary" type="submit" name="search">Search</button>
</span>
</div></center>
</form>
<br><center><a href="<?php echo Config::$_PAGE_URL ?>complaints"><< Back</a></center>
<?php		
} else {
$w = Config::$g_con->prepare("SELECT `id`,`Model`,`name`,`Level`,`Member`,`Status`,`lastOn` FROM `users` WHERE `name` LIKE ? LIMIT 10");
$w->execute(array('%'.$_POST['user'].'%'));
if(!$w->rowCount()) Config::gotoPage("complaints/create",0,"warning","We couldn't find any users with a name like u wrote!");
while($user = $w->fetch(PDO::FETCH_OBJ)) { 
if($user->id == Config::getUser()) continue;
?>

		<div class="pull-left" style="margin-right: 20px">
			<a href="<?php echo Config::$_PAGE_URL ?>complaints/create/<?php echo $user->name ?>"><div class="panel" style="padding: 10px; color: black">
				<table class="table table-hover">
				<tbody>
					<tr>
						<ul class="list-unstyled list-contacts">
							<div class="media" style="float: none">
								<center>
								<img src="<?php echo Config::$_PAGE_URL ?>assets/img/avatars/<?php echo $user->Model ?>.png" class="picture" alt="" style="border: 2px solid <?php echo ($user->Status ? 'green' : 'red') ?>">
								</center>
							</div>
						</ul>
					</tr>
					<tr>
						<center>
							<?php echo Config::formatName($user->name); ?><br>
							<small>
								<?php echo 'Level: ' . $user->Level; ?><br>
								<?php echo Config::justFactionName($user->Member); ?><br>
								<?php echo Config::timeAgo($user->lastOn); ?>
							</small>
						</center>
					</tr>
				</tbody>
				</table>
			</div></a>
		</div>
		<?php }
		echo '<div class="clearfix"></div><div class="alert alert-warning alert-dismissible" role="alert">
			<i class="fa fa-info-circle"></i> Click on one of those boxes to start making a complaint about him!
		</div><center><a href="'.Config::$_PAGE_URL.'complaints/create"><< Back</a></center>';
		
	}
} else {
	$wcjd = Config::$g_con->prepare('SELECT `id`,`name`,`Member`,`Admin`,`Helper`,`lastOn`,`ConnectedTime`,`Warns`,`RegisterDate`,`Level`,`CChar` FROM `users` WHERE `name` = ?');
	$wcjd->execute(array(Config::$_url[2]));
	if(!$wcjd->rowCount()) Config::gotoPage("complaints/create",0,"warning","We couldn't find any users with a name you provide in link!");
	$show = $wcjd->fetch(PDO::FETCH_OBJ);
	if($show->id == Config::getUser()) Config::gotoPage("complaints/create",0,"warning","You cant make a complaint to yourself!");
	if(isset($_POST['submit'])) {
		if(strlen($_POST['about']) < 30) Config::gotoPage("complaints/create/".$show->name."",0,"warning","You dont think less than 30 letters is too short?");
		if($_POST['category'] == 0 || $_POST['category'] == 1 || $_POST['category'] == 3)
		{
			$wcjd = Config::$g_con->prepare('INSERT INTO `wcode_complaints` (`UserID`,`UserName`,`AccusedID`,`AccusedName`,`Text`,`dovezi`,`Motiv`,`Category`) VALUES (?,?,?,?,?,?,?,1)');
			$wcjd->execute(array(Config::getUser(),Config::getData("users","name",Config::getUser()),$show->id,$show->name,$_POST['about'],$_POST['dovezi'],$_POST['motiv']));
			Config::gotoPage("complaints",0,"success","Your complaint has been posted! Wait for admin response.");
			$link = Config::$_PAGE_URL.'complaints';
		}
		else
		{
			$wcjd = Config::$g_con->prepare('INSERT INTO `wcode_complaints` (`UserID`,`UserName`,`AccusedID`,`AccusedName`,`Text`,`dovezi`,`Motiv`,`Faction`,`Category`) VALUES (?,?,?,?,?,?,?,?,1)');
			$wcjd->execute(array(Config::getUser(),Config::getData("users","name",Config::getUser()),$show->id,$show->name,$_POST['about'],$_POST['dovezi'],$_POST['motiv'],$show->Member));
			Config::gotoPage("complaints/factions/".$show->Member."",0,"success","Your complaint has been posted! Wait for faction leader response!");
			$link = Config::$_PAGE_URL.'complaints/factions/'.$show->Member.'/list';
		}
		$notif = ''.Config::getName(Config::getUser(),false).' made an Faction complaint against you! Check it out.';
		Config::makeNotification($show->id,$show->name,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
	}
	?>
<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Complaint Info
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<h4>Player reclamat</h4>
<ul>
<?php
echo '
<li>Nickname: '.Config::formatName($show->name).'</li>
<li>Level: '.$show->Level.'</li>
<li>Ore jucate: '.$show->ConnectedTime.'</li>
'; 
?>
</ul>
<h4>Info</h4>
<ul>
<li>Inainte de a reclama un player, cititi <a href="http://bugged.ro/regulament">regulamentul serverului</a>. Daca faceti reclamatie unui lider, cititi si <a href="http://bugged.ro/lideri">regulamentul liderilor</a>.</li>
<li>Puteti uploada imagini pe site-uri ca <a href="https://imgur.com" target="_blank">imgur</a></li>
</ul>
</div>
</div>
</div>
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Creaza o reclamatie
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<form method="POST" action="">
<div class="form-group">
<label class="">Motiv reclamatie</label><br>
<select class="form-control" name="motiv">
<option value="-1">Niciunul</option>
<option value="1" style="color:grey;">Limbaj</option>
<option value="2" style="color:grey;">Deathmatch</option>
<option value="3" style="color:grey;">Hacking</option>
<option value="4" <?php echo ($show->Admin ? 'style="color:grey;"' : 'disabled style="color:red;"') ?>>Abuz</option>
</select>
<br>
<label for="links">Dovezi (screenshot-uri, video-uri)</label>
<textarea class="form-control" rows="2" name="dovezi" cols="50" id="links"></textarea>
<br>
<label for="desc">Detalii</label>
<textarea class="form-control" rows="5" name="about" cols="50" id="desc"></textarea>
<br>
<input class="btn btn-small btn-danger" type="submit" name="submit" value="Post complaint">
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>

	<div class="clearfix"></div>
	<?php
}
} else if(Config::$_url[1] == "factions" && isset(Config::$_url[2]) && is_numeric(Config::$_url[2])) {
?>
<table class="table table-minimal">
<thead>
	<tr>
		<th>#</th>
		<th>NAME</th>
		<th>AGAINST BY</th>
		<th>FACTION</th>
		<th>STATUS</th>
		<th>DATE</th>
		<th>VIEW</th>
	</tr>
</thead>
<tbody>
<?php
$wcs = Config::$g_con->prepare('SELECT `ID`,`UserName`,`AccusedName`,`Category`,`Faction`,`Status`,`Date` FROM `wcode_complaints` WHERE `Faction` = ? ORDER BY `Date` DESC '.Config::limit().'');
$wcs->execute(array(Config::$_url[2]));
while($compl = $wcs->fetch(PDO::FETCH_OBJ)) {
	echo '
		<tr>
			<td>'.$compl->ID.'</td>
			<td>'.Config::formatName($compl->UserName).'</td>
			<td>'.Config::formatName($compl->AccusedName).'</td>
			<td>'.Config::justFactionName($compl->Faction).'</td>
			<td>'.(!$compl->Status ? '<span class="label label-success label-transparent">OPENED</span>' : '<span class="label label-danger label-transparent">CLOSED</span>').'</td>
			<td>'.$compl->Date.'</td>
			<td><a href="'.Config::$_PAGE_URL.'complaints/view/'.$compl->ID.'">View</a></td>
		</tr>
	';
}
?>
</tbody>
</table><br>
<?php
echo Config::create(Config::rows_s("wcode_complaints","ID","Faction",Config::$_url[2]));
} else Config::gotoPage("complaints", 0, "danger", "You entered on a wrong page!");
?>
</div>