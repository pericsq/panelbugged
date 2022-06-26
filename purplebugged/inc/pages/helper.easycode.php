<?php
if(!Config::isLogged()) Config::gotoPage("", 0, "danger", "You must be logged in to can access the page!");
date_default_timezone_set('Europe/Bucharest');
$time = time();
?>
<?php if(!isset(Config::$_url[1])) { ?>
<div class="container-fluid">
<div class="panel" style="padding: 10px">
<a href="<?php echo Config::$_PAGE_URL ?>helper/apply"><button type="button" class="btn btn-success" title="Create"><i class="ti-pencil"></i>Apply</button></a><br><br><table class="table table-minimal">
	<thead>
		<tr>
			<th>#</th>
			<th>NAME</th>
			<th>CATEGORY</th>
			<th>STATUS</th>
			<th>DATE</th>
			<th>VIEW</th>
		</tr>
	</thead>
	<tbody>
<?php
if(Config::isAdmin(Config::getUser(), 4)) {
$s = Config::$g_con->prepare('SELECT * FROM `kenny_happs` WHERE `Type` = 1 ORDER BY `ID` DESC, `Status` ASC');
$s->execute(array());
}
else { 
$s = Config::$g_con->prepare('SELECT * FROM `kenny_happs` WHERE (`Type` = 1 && `UserID` = ?) ORDER BY `ID` DESC, `Status` ASC');
$s->execute(array(Config::getData("users","id",Config::getUser())));
}
while($app = $s->fetch(PDO::FETCH_OBJ)) { ?>     	
			<tr>
				<td><?php echo $app->ID ?></td>
				<td><?php echo Config::formatName($app->UserName) ?></td>
				<td><span class="label label-default label-transparent">HELPER</span></td>
				<td>
<?php if ($app->Status == 0) { ?>				
<span class="label label-primary label-transparent">Pending</span>
<?php } ?>	
<?php if ($app->Status == 1) { ?>				
<span class="label label-danger label-transparent">For tests</span>
<?php } ?>
<?php if ($app->Status == 2) { ?>				
<span class="label label-success label-transparent">Accepted</span>
<?php } ?>	
<?php if ($app->Status == 3) { ?>				
<span class="label label-danger label-transparent">Rejected</span>
<?php } ?>				
				</td>
				<td><?php echo $app->Date ?></td>
				<td><a href="<?php echo Config::$_PAGE_URL ?>helper/view/<?php echo $app->ID ?>">View</a></td>
			</tr>
<?php } ?>			
			</tbody>
</table><br>
<ul class="pagination" style="margin:0px;display:0;float:right"></ul><div class="clearfix"></div></div>					</div>

<?php } else if(Config::$_url[1] == "view" && isset(Config::$_url[2]) && is_numeric(Config::$_url[2])) { ?>

<?php
    $ke = Config::$g_con->prepare('SELECT * FROM `users` WHERE `name` = ?');
    $ke->execute(array(Config::getData('kenny_happs','UserName',Config::$_url[2])));
    $userapp = $ke->fetch(PDO::FETCH_OBJ);
?>
	
	
<?php 
    $k = Config::$g_con->prepare('SELECT * FROM `kenny_happs` WHERE `ID` = ?');
    $k->execute(array(Config::$_url[2]));
    if(!$k->rowCount()) { echo 'Error matched.'; }
    $app = $k->fetch(PDO::FETCH_OBJ);
?>	

<div class="container-fluid">

 <?php
if( (Config::isAdmin(Config::getUser(), 4)) || ($userapp->name == Config::getData("users","name",Config::getUser()))) { 
?> 

<?php if(Config::isAdmin(Config::getUser(), 4)) { ?>

<?php if(isset($_POST['reject'])) {
$reason = htmlspecialchars($_POST['reason']);

$kenny = Config::$g_con->prepare('UPDATE `kenny_happs`  SET `Reason` = ?, `ActionBy` = ?, `AnswerDate` = ?, `Status` = 3 WHERE `ID` = ?');
$kenny->execute(array($reason, Config::getData("users","name",Config::getUser()), date('d.m.Y H:i', $time), Config::$_url[2]));

?>
<meta http-equiv="refresh" content="1">
<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> Application was rejected.
			</div>
<?php } ?>

<?php if(isset($_POST['fortest'])) {
$reason = htmlspecialchars($_POST['reason']);

$kenny = Config::$g_con->prepare('UPDATE `kenny_happs`  SET `Reason` = ?, `ActionBy` = ?, `AnswerDate` = ?, `Status` = 1 WHERE `ID` = ?');
$kenny->execute(array($reason, Config::getData("users","name",Config::getUser()), date('d.m.Y H:i', $time), Config::$_url[2]));

?>
<meta http-equiv="refresh" content="1">
<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> Application was accepted for test.
			</div>
<?php } ?>

<?php if(isset($_POST['accept'])) {
$reason = htmlspecialchars($_POST['reason']);

$kenny = Config::$g_con->prepare('UPDATE `kenny_happs`  SET `Reason` = ?, `ActionBy` = ?, `AnswerDate` = ?, `Status` = 2 WHERE `ID` = ?');
$kenny->execute(array($reason, Config::getData("users","name",Config::getUser()), date('d.m.Y H:i', $time), Config::$_url[2]));

?>
<meta http-equiv="refresh" content="1">
<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> Application was accepted.
			</div>
<?php } ?>


<?php } ?>
<style>
.card-header-text{
    margin-bottom: 0;
    font-size: 1rem;
    color: rgba(51, 51, 51, 0.85);
    text-transform: uppercase;
    font-weight: 600;
    display: inline-block;
    vertical-align: middle;
}
.card-header{
    padding: 20px;
    background-color: transparent;
    color: #757575;
}
.card{
    margin-bottom: 30px;
    border: none;
    box-shadow: 0 0 1px 2px rgba(0, 0, 0, 0.05), 0 -2px 1px -2px rgba(0, 0, 0, 0.04), 0 0 0 -1px rgba(0, 0, 0, 0.05);
}
.contact-mobile {
    position: relative;
    display: block;
    background-color: #3d4f58;
    cursor: pointer;
}
.contact-card-info{
    position: relative;
    border-bottom: 1px solid #4c626d;
}
.contact-card-info a{
    font-size: 14px;
}
.contact-mobi-front a{color:#fff;}
.contact-mobi-front .front-img img{
    margin-right: 10px;
    height: 50px;
    width: 50px;
}
.contact-card-button{text-align: center;padding: 15px 0;}
.contact-card-button i{margin-right: 5px;}
.contact-mobi-front .img-circle {
    width:150px;
    height: 150px;
    padding: 2px;
    border: 1px solid #757575;
}
.contact-details-front-img {
    text-align: center;
    padding: 20px 0;
}
.contact-details-front-img h4{
    margin-top: 10px;
}
.contact-mobi-front {
    position: relative;
}
.front-img {
    position: absolute;
    bottom: 15px;
}
.front-img > img {
    margin-left: 17px;
}
.contact-card-info {
    padding: 10px 37px 10px 40px;
}
.contact-card-info i {
    padding: 10px;
    position: absolute;
    left: 5px;
    top: 5px;
}
.width-100 {
    width: 100%;
}

ul {
    list-style: none;
    padding: 0;
    margin-bottom: 0;
}
</style>
<div class="panel" style="padding: 10px">		


<div class="col-md-3">
			<div class="panel" style="padding: 10px">
				<h5>Creator short details</h5><hr>
				<center>
				<div class="media">
					<img src="<?php echo Config::$_PAGE_URL ?>/assets/img/avatars/<?php echo $userapp->CChar ?>.png" class="picture" alt="" style="border: 2px solid #79afbe; border-radius: 90px;">
				</div>
				<?php echo Config::formatName($userapp->name) ?>
				</center>
				<p></p><div class="pull-right"><b><?php echo Config::timeAgo($userapp->lastOn) ?></b></div>Last login<p></p>
				<p></p><div class="pull-right"><b><?php echo $userapp->Level ?></b></div>Level<p></p>
				<p></p><div class="pull-right"><b><?php echo $userapp->ConnectedTime ?></b></div>Hours played<p></p>
				<p></p><div class="pull-right"><b><?php echo $userapp->FPunish ?>/30</b></div>FPunish<p></p>
				<p></p><div class="pull-right"><b><?php echo $userapp->Warnings ?>/3</b></div>Warnings<p></p>
				<p></p><div class="pull-right"><b><?php echo $userapp->RegisterDate ?></b></div>Registered<p></p>
				<hr>
				<center>
				<?php if ($app->Status == 0) { ?>				
<span class="label label-primary label-transparent">Pending</span>
<?php } ?>	
<?php if ($app->Status == 1) { ?>				
<span class="label label-warning label-transparent">For tests</span><br>
<span class="label label-default label-transparent"><?php echo Config::timeAgo($app->AnswerDate) ?></span><br>
<span class="label label-default label-transparent">by <?php echo $app->ActionBy ?></span>
<?php } ?>
<?php if ($app->Status == 2) { ?>				
<span class="label label-success label-transparent">Accepted</span><br>
<span class="label label-default label-transparent"><?php echo Config::timeAgo($app->AnswerDate) ?></span><br>
<span class="label label-default label-transparent">by <?php echo $app->ActionBy ?></span>
<?php } ?>	
<?php if ($app->Status == 3) { ?>				
<span class="label label-danger label-transparent">Rejected</span><br>
<span class="label label-default label-transparent"><?php echo Config::timeAgo($app->AnswerDate) ?></span><br>
<span class="label label-default label-transparent">by <?php echo $app->ActionBy ?></span>
<?php } ?>	
				<hr>
				<center><b><?php echo Config::timeAgo($app->Date) ?><br><?php echo $app->Date ?></b></center>

<?php if(Config::isAdmin(Config::getUser(), 4)) { ?>		
<?php if ($app->Status == 0) { ?>
<form method="POST">
<hr>
<input placeholder="Reason" class="form-control" type="text" name="reason" style="width: 100%">
<br>
<button type="submit" class="btn btn-warning btn-block" name="fortest"><i class="ti-share-alt"></i> For test</button>	
<button type="submit" class="btn btn-danger btn-block" name="reject"><i class="ti-share-alt"></i> Reject</button>
</form>	
<?php } ?>
<?php if ($app->Status == 1) { ?>
<form method="POST">
<hr>
<input placeholder="Reason" class="form-control" type="text" name="reason" style="width: 100%">
<br>
<button type="submit" class="btn btn-success btn-block" name="accept"><i class="ti-share-alt"></i> Accept</button>
<button type="submit" class="btn btn-danger btn-block" name="reject"><i class="ti-share-alt"></i> Reject</button>	
</form>
<?php } ?>	
<?php } ?>
				
			</div>		
			
			</div>
			
			
		<div class="col-md-9">
			<div class="panel" style="padding: 10px">
			
<?php if ($app->Status == 1 || $app->Status == 2 || $app->Status == 3) { ?>		
<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> Reason: <?php echo $app->Reason ?>
			</div>
<?php } ?>			
				<?php
				echo '<h5>Questions & answeres</h4><hr>';
					$row = explode("|", $app->Answers);
					for ($x = 0; $x < $app->Questions; $x++) {
						$show = explode(":", $row[$x]);
						echo'<div class="panel" style="padding: 10px; margin-bottom: 7px; background-color: #4c626d;border: 1px solid #5f8591; border-radius: 5px;">
							<b>'.Config::xss(Config::clean($show[0])).'</b><br>'.Config::xss(Config::clean($show[1])).'
						</div>';
					}
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		</div>
<script>
$(function()
{
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
});
</script>					

<?php } else { ?>
<?php Config::gotoPage("", 0, "danger", "Don't have acces to this application!"); ?>
<?php } ?>
</div>
	
<?php } else if(Config::$_url[1] == "apply") { ?>
	
<?php 
$k3 = Config::$g_con->prepare('SELECT * FROM `wcode_editables` WHERE `Form` = "StatusHelper"');  
$k3->execute(array());
$statushelper = $k3->fetch(PDO::FETCH_OBJ); 
?>	

<?php 
$k4 = Config::$g_con->prepare('SELECT * FROM `wcode_editables` WHERE `Form` = "HoursHelper"');  
$k4->execute(array());
$hourshelper = $k4->fetch(PDO::FETCH_OBJ); 
?>		
	
<?php if ($hourshelper->Text > Config::getData("users","ConnectedTime",Config::getUser())) { ?>
<?php echo Config::gotoPage("", 0, "danger", "You must have at least ".$hourshelper->Text." hours played to apply for the helper."); ?>
<?php } else { ?>	
	
<?php if ($statushelper->Text == 1) { ?>
<?php echo Config::gotoPage("", 0, "danger", "Helper applications are closed!"); ?>
<?php } else { ?>		
	
<?php if(Config::isAdmin(Config::getUser(), 1)) { ?>
<?php echo Config::gotoPage("", 0, "danger", "You are already a admin."); ?>
<?php } else { ?>

<?php if(Config::getData("users","Helper",Config::getUser()) > 0) { ?>
<?php echo Config::gotoPage("", 0, "danger", "You are already a helper."); ?>
<?php } else { ?>	
	
<?php 
$qk = Config::$g_con->prepare("SELECT * FROM `kenny_hquestions` WHERE `Type` = 1");
$qk->execute(array());
$count = 1;
?>

<div class="panel" style="padding: 10px">

<?php 
if(isset($_POST['app_send'])) {
				$checked = 0;
				$questions = "";
				for ($x = 1; $x <= $_SESSION['questions']; $x++) {
					if(strlen($_POST['question'.$x.'']) > 1) 
					{ 
						$checked++; 
						if($x == $_SESSION['questions']) $questions = $questions . $_POST['ques'.$x.''] . ':' . $_POST['question'.$x.'']; 
						else $questions = $questions . $_POST['ques'.$x.''] . ':' . $_POST['question'.$x.''] . '|'; 
					}
				}
				if($checked != $_SESSION['questions']) { Config::gotoPage("helper/apply",0,"warning","Please complete all the questions!"); }
				else {
					$wcd = Config::$g_con->prepare('INSERT INTO `kenny_happs` (`UserID`,`UserName`,`Type`,`Answers`,`Questions`,`Date`) VALUES (?,?,?,?,?,?)');
					$wcd->execute(array(Config::getUser(),Config::getData("users","name",Config::getUser()),1,$questions,$_SESSION['questions'], date('d.m.Y H:i', $time)));
					Config::gotoPage("helper/",0,"success","Your application has been posted with success!");
					$_SESSION['questions'] = -1;
				}
			}
?>

<h5>Create an application for <i>Helper</i></h5>
<form method="POST">
<?php while($question = $qk->fetch(PDO::FETCH_OBJ)) { ?>
						<div class="input-group" style="margin-bottom: 8px">
							<span class="input-group-addon">#<?php echo $count ?></span>
							<input type="hidden" name="ques<?php echo $count ?>" value="<?php echo $question->Question ?>">
							<input type="text" class="form-control" placeholder="<?php echo $question->Question ?>" name="question<?php echo $count ?>">
						</div>
<?php 
$_SESSION['questions'] = $count;
$count++; 
} ?>
<center>
					<button type="submit" class="btn btn-info" name="app_send"><i class="fa fa-rocket"></i>
						<span>Send application</span>
					</button>
				</center>
</form>
</div>		

<?php } } ?>
<?php } } ?>
		
<?php } ?>