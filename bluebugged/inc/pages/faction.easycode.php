<?php
if(isset(Config::$_url[1])) echo '<div class="panel" style="padding: 10px">';
	
if(!isset(Config::$_url[1])) {
		
		$wcs = Config::$g_con->prepare('SELECT `ID`,`Name`,`level`,`MaxMembers`,App FROM `factions`');
		$wcs->execute();
		while($factions = $wcs->fetch(PDO::FETCH_OBJ)) {
			$jj = Config::$g_con->prepare('SELECT `id` FROM `users` WHERE `Member` = ?'); $jj->execute(array($factions->ID));
			$app = Config::$g_con->prepare('SELECT `id` FROM `faction_apply` WHERE `FactionID` = ? AND `Status` = 0'); $app->execute(array($factions->ID));
			$compl = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Faction` = ? AND `Status` = 0'); $compl->execute(array($factions->ID));
		echo '<div class="page-content">
		<div class="row-fluid">
		<div class="span12">
		<div class="row">
		<div class="col-lg-9">
		<div class="card  bg-purple text-white">
		<div class="card-body">
		<p class="card-title typography-headline">
		Lista factiuni
		</p>
		</div>
		</div>
		<table class="table table-dark table-responsive-sm">
		<thead>
		<tr>
		<th scope="col">Nume</th>
		<th scope="col" class="">Membri</th>
		<th scope="col" class="">Status Aplicatie</th>
		<th scope="col" class="">Level Necesar</th>
		</tr>
		</thead>
		';
		$wcs = Config::$g_con->prepare('SELECT `ID`,`Name`,`level`, `MaxMembers`,`App` FROM `factions`');
		$wcs->execute();
		while($factions = $wcs->fetch(PDO::FETCH_OBJ)) {
			$jj = Config::$g_con->prepare('SELECT `id` FROM `users` WHERE `Member` = ?'); $jj->execute(array($factions->ID));
			$app = Config::$g_con->prepare('SELECT `id` FROM `faction_apply` WHERE `FactionID` = ? AND `Status` = 0'); $app->execute(array($factions->ID));
			$compl = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Faction` = ? AND `Status` = 0'); $compl->execute(array($factions->ID));
			echo '
			<tbody>
			<tr class="">
				<td>'.$factions->Name.'</td>
				<td>'.$jj->rowCount().' / '.$factions->MaxMembers.'</td>
				<td>';						
				if(!Config::isLogged()) echo '<span class="label label-rounded label-danger">You need an account for that.</span>';
				else if(Config::isLogged() && $factions->level > Config::getData("users","Level",Config::getUser())) echo '<a class="btn btn-small btn-danger">Ai nevoie de level 10 pentru a aplica in aceasta factiune</a>';
				else if($factions->App) echo '<span class="text-warning">Aplicatii inchise</span>';
				else if(Config::getData("users","Member",Config::getUser())) echo '<span class="label label-rounded label-danger">You already belong to a faction.</span>';
				else if(!$factions->App) echo '<a class="btn btn-small btn-success" href="'.Config::$_PAGE_URL.'factions/applications/'.$factions->ID.'/create">Aplica!</a>';
				else echo '<span class="label label-rounded label-danger">You are not authorized to do anything.</span>';					
				echo '
				<td>Level '.$factions->level.'</td>
			</tr>
			</tbody>
             ';
			
		}
		echo '
		</table>
		</div>
		';
		echo '
		<div class="col-lg-3">
		<div class="card  bg-purple text-white">
		<div class="card-body">
		<p class="card-title typography-headline">
		Alte Chestii
		</p>
		</div>
		</div>
		<div class="card bg-dark-2 text-white">
		<div class="card-body">
		<div class="card-text">
		<h4><a href="goals">Faction activity reports</a></h4>
		<br>
		</div>
		</div>
		</div>
		</div>
';
			
		}

} else if(Config::$_url[1] == "members" && isset(Config::$_url[2])) {
	$wcs = Config::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
	$wcs->execute(array(Config::$_url[2]));
	if(!$wcs->rowCount()) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
	if(Config::$_url[2] == 0) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
	$users = Config::$g_con->prepare('SELECT `id`,`name`,`Rank`,`FWarn`,`lastOn`,`Model`,`Status` FROM `users` WHERE `Member` = ? ORDER BY `Rank` DESC');
	$users->execute(array(Config::$_url[2],Config::$_url[2]));
	if(Config::getData("users","Leader",Config::getUser()) == Config::$_url[2] || Config::isAdmin(Config::getUser())) {
		if(isset($_POST['rank_up'])) {
			if(Config::getData("users","Rank",$_POST['rank_up']) < 7) {
				$wcode = Config::$g_con->prepare('UPDATE `users` SET `Rank` = `Rank`+1 WHERE `id` = ?');
				$wcode->execute(array($_POST['rank_up']));
				Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"success","Changes has took effect. (Rank up)");
				$link = Config::$_PAGE_URL.'profile/'.Config::getName($_POST['rank_up'],false).'';
				$notif = ''.Config::getName(Config::getUser(),false).' promoted you in faction rank!';
				Config::makeNotification($_POST['rank_up'],Config::getName($_POST['rank_up'],false),$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
			} else Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"danger","Member reached maximum rank level!");
		}
		if(isset($_POST['rank_down'])) {
			if(Config::getData("users","Rank",$_POST['rank_down']) > 1) {
				$wcode = Config::$g_con->prepare('UPDATE `users` SET `Rank` = `Rank`-1 WHERE `id` = ?');
				$wcode->execute(array($_POST['rank_down']));
				Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"success","Changes has took effect. (Rank down)");
				$link = Config::$_PAGE_URL.'profile/'.Config::getName($_POST['rank_down'],false).'';
				$notif = ''.Config::getName(Config::getUser(),false).' demoted you in faction rank!';
				Config::makeNotification($_POST['rank_down'],Config::getName($_POST['rank_down'],false),$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
			} else Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"danger","Member reached minimum rank level!");
		}
		if(isset($_POST['fw_up'])) {
			if(Config::getData("users","FWarn",$_POST['fw_up']) < 3) {
				$wcode = Config::$g_con->prepare('UPDATE `users` SET `FWarn` = `FWarn`+1 WHERE `id` = ?');
				$wcode->execute(array($_POST['fw_up']));
				Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"success","Changes has took effect. (FWarn up)");
				$link = Config::$_PAGE_URL.'profile/'.Config::getName($_POST['fw_up'],false).'';
				$notif = ''.Config::getName(Config::getUser(),false).' gave you an faction warn!';
				Config::makeNotification($_POST['fw_up'],Config::getName($_POST['fw_up'],false),$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
			} else Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"danger","Member reached maximum faction warn points and must be uninvited!");
		}
		if(isset($_POST['fw_down'])) {
			if(Config::getData("users","FWarn",$_POST['fw_down']) > 0) {
				$wcode = Config::$g_con->prepare('UPDATE `users` SET `FWarn` = `FWarn`-1 WHERE `id` = ?');
				$wcode->execute(array($_POST['fw_down']));
				Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"success","Changes has took effect. (FWarn down)");
				$link = Config::$_PAGE_URL.'profile/'.Config::getName($_POST['fw_down'],false).'';
				$notif = ''.Config::getName(Config::getUser(),false).' reduced you one faction warn point!';
				Config::makeNotification($_POST['fw_down'],Config::getName($_POST['fw_down'],false),$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
			} else Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"danger","Member reached minimum faction warn points!");
		}
		if(isset($_POST['member_uninvite'])) {
			$wcode = Config::$g_con->prepare('UPDATE `users` SET `Member` = 0, `Rank` = 0, `FP` = 0, `FWarn` = 0 WHERE `id` = ?');
			$wcode->execute(array($_POST['member_uninvite']));
			Config::gotoPage('faction/members/'.Config::$_url[2].'',0,"success","Changes has took effect. (Player has been excluded from group)");
			$link = Config::$_PAGE_URL.'profile/'.Config::getName($_POST['member_uninvite'],false).'';
			$notif = ''.Config::getName(Config::getUser(),false).' uninvited you from the faction!';
			Config::makeNotification($_POST['member_uninvite'],Config::getName($_POST['member_uninvite'],false),$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
		}
	}
	?>
	<h5><?php echo 'View members <i>(' . Config::justFactionName(Config::$_url[2]) . ')</i>' ?></h5>
	<table class="table table-minimal">
		<thead>
			<tr>
				<th>#</th>
				<th>USER</th>
				<th>RANK</th>
				<th>FWARN</th>
				<th>LAST LOGIN</th>
				<?php
				if(Config::getData("users","Leader",Config::getUser()) == Config::$_url[2]) echo '<th>ACTIONS</th>';
				?>
			</tr>
		</thead>
		<tbody>
		<?php
		while($user = $users->fetch(PDO::FETCH_OBJ))
		{
			echo '
			<tr>
				<td>
					<ul class="list-unstyled list-contacts"><div class="media">
						<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.$user->Model.'.png" class="picture" alt="" style="border: 2px solid #79afbe">
						<span class="status '.($user->Status ? 'online' : '').'"></span>
					</div></ul>
				</td>
				<td>'.Config::formatName($user->name).'</td>
				<td>'.$user->Rank.'</td>
				<td>'.$user->FWarn.'/3</td>
				<td>'.Config::timeAgo($user->lastOn).'<br><i>('.$user->lastOn.')</i></td>';
				if(Config::getData("users","Leader",Config::getUser()) == Config::$_url[2]) {
					echo '<td><form method="post" action="">
					<button type="submit" name="rank_up" value="'.$user->id.'" class="btn btn-primary btn-xs"><i class="fa fa-arrow-up"></i>Rank</button>
					<button type="submit" name="rank_down" value="'.$user->id.'" class="btn btn-primary btn-xs"><i class="fa fa-arrow-down"></i>Rank</button>
					<button type="submit" name="member_uninvite" value="'.$user->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i>Uninvite</button>
					<button type="submit" name="fw_up" value="'.$user->id.'" class="btn btn-default btn-xs"><i class="fa fa-arrow-up"></i>FWarn</button>
					<button type="submit" name="fw_down" value="'.$user->id.'" class="btn btn-default btn-xs"><i class="fa fa-arrow-down"></i>FWarn</button>
					</form></td>';
				}
				if(Config::isAdmin(Config::getUser(), 5)) echo '<td><form method="post" action=""><button type="submit" name="member_uninvite" value="'.$user->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i>Uninvite</button></form></td>';
			echo '</tr>';
		}
		?>
		</tbody>
	</table>
	<?php
} else if(Config::$_url[1] == "logs" && isset(Config::$_url[2])) {
	$wcs = Config::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
	$wcs->execute(array(Config::$_url[2]));
	if(!$wcs->rowCount()) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
	if(Config::$_url[2] == 0) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
	?>
	<h5><?php echo 'View logs of faction <i>(' . Config::justFactionName(Config::$_url[2]) . ')</i>' ?></h5>
	<table class="table">
		<tbody>
             <?php 
             $qsz = Config::$g_con->prepare('SELECT * FROM `factionlog` WHERE `factionid` = ? ORDER  BY `ID` DESC');
             $qsz->execute(array(Config::$_url[2])); ?>
             <?php while($rows = $qsz->fetch(PDO::FETCH_OBJ)) { ?>
             <tr>
             	<td><?php echo $rows->player ;?></td>
               <td><?php echo $rows->time; ?></td>
                <td><?php echo $rows->action; ?></td>
               </tr>
              <?php } ?>
		</tbody>
     </table>
	<?php
} else if(Config::$_url[1] == "applications" && isset(Config::$_url[2]) && isset(Config::$_url[3])) {
	if(!Config::isLogged()) { Config::gotoPage("factions"); Config::createSN("danger","You are not logged in!"); }
	$wcs = Config::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
	$wcs->execute(array(Config::$_url[2]));
	if(Config::$_url[2] == "view" && isset(Config::$_url[3]))
	{
		$wcj = Config::$g_con->prepare('SELECT * FROM `wcode_applications` WHERE `id` = ?');
		$wcj->execute(array(Config::$_url[3]));
		if(!$wcj->rowCount()) { Config::gotoPage("factions"); Config::createSN("danger","We couldn't find any application!"); }
		$app = $wcj->fetch(PDO::FETCH_OBJ);
		
		$wcjd = Config::$g_con->prepare('SELECT `id`,`name`,`Model`,`Warnings`,`lastOn`,`Level`,`RegisterDate`,`FP`,`Hours`,`Rank` FROM `users` WHERE `id` = ?');
		$wcjd->execute(array($app->UserID));
		$user = $wcjd->fetch(PDO::FETCH_OBJ);
		?>
		<div class="col-md-3">
			<div class="panel" style="padding: 10px">
				<?php
				if(Config::isLogged())
				{
					if(Config::getData("users","Member",Config::getUser()) == $app->FactionID && Config::getData("users","Rank",Config::getUser()) >= Config::$_LEADER_RANK)
					{
						if($app->Status == 0)
						{
							if(isset($_POST['accepted']))
							{
								$vv = Config::$g_con->prepare('UPDATE `wcode_applications` SET `Status` = "1",`ActionBy` = ? WHERE `id` = ?');
								$vv->execute(array(Config::getNameFromID(Config::getUser()),Config::$_url[3])); $app->Status = 1;
								
								$notif = 'Your faction application has been accepted!';
								$link = Config::$_PAGE_URL.'faction/applications/view/' . Config::$_url[3];
								Config::makeNotification($app->UserID,$app->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
							}
							if(isset($_POST['rejected']))
							{
								$vv = Config::$g_con->prepare('UPDATE `wcode_applications` SET `Status` = "2",`ActionBy` = ? WHERE `id` = ?');
								$vv->execute(array(Config::getNameFromID(Config::getUser()),Config::$_url[3])); $app->Status = 2;
								
								$notif = 'Your faction application has been rejected!';
								$link = Config::$_PAGE_URL.'faction/applications/view/' . Config::$_url[3];
								Config::makeNotification($app->UserID,$app->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
							}
						}
					}
				}
				if(!$app->Status)$status = '<span class="label label-warning label-transparent">OPENED</span>';
				else if($app->Status == 1)$status = '<span class="label label-success label-transparent">CLOSED & ACCEPTED</span>';
				else $status = '<span class="label label-default label-transparent">CLOSED & REJECTED</span>';
				echo '<h5>Creator short details</h5><hr>
				<center>
				<div class="media">
					<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.$user->Model.'.png" class="picture" alt="" style="border: 2px solid #79afbe; border-radius: 90px;">
				</div>
				'.Config::formatName($user->name).'
				</center>
				<p><div class="pull-right"><b>'.Config::timeAgo($user->lastOn).'</b></div>Last login</p>
				<p><div class="pull-right"><b>'.$user->Level.'</b></div>Level</p>
				<p><div class="pull-right"><b>'.$user->Hours.'</b></div>Hours played</p>
				<p><div class="pull-right"><b>'.$user->FP.'/30</b></div>FP</p>
				<p><div class="pull-right"><b>'.$user->Warnings.'/3</b></div>Warnings</p>
				<p><div class="pull-right"><b>'.$user->RegisterDate.'</b></div>Registered</p>
				<hr>
				<center>'.$status.'</center>';
				if($app->Status != 0) echo '<p><div class="pull-right"><b>'.Config::formatName($app->ActionBy).'</b></div>Responded</p>';
				echo '<hr><center><b>'.Config::timeAgo($app->Date).'<br>'.$app->Date.'</b></center>
			</div>';
			if(Config::isLogged())
			{
				if(Config::getData("users","Member",Config::getUser()) == $app->FactionID && Config::getData("users","Rank",Config::getUser()) >= Config::$_LEADER_RANK)
				{
					if($app->Status == 0)
					{
						echo '<form method="post">
							<button type="submit" class="btn btn-success btn-block" name="accepted" value="1" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="This action represent only status of the Application." data-original-title=""><i class="ti-check" ></i> Closed & <b>Accepted</b></button>
							<button type="submit" class="btn btn-success btn-block" name="rejected" value="2"><i class="ti-share-alt"></i> Closed & <b>Rejected</b></button>
						</form>';
					}
				}
			}
			?>
		</div>
		<div class="col-md-9">
			<div class="panel" style="padding: 10px">
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
		<?php		
	} else {
		if(!$wcs->rowCount()) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
		if(Config::$_url[2] == 0) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
		if(Config::$_url[3] == "list") {
			$wcd = Config::$g_con->prepare('SELECT * FROM `wcode_applications` WHERE `FactionID` = ? ORDER BY `id` DESC '.Config::limit().'');
			$wcd->execute(array(Config::$_url[2]));
			?>
			<table class="table table-minimal">
				<thead>
					<tr>
						<th>#</th>
						<th>USER</th>
						<th>FACTION</th>
						<th>STATUS</th>
						<th>DATE</th>
						<th>ACTION</th>
					</tr>
				</thead>
				<tbody>
				<?php
				while($app = $wcd->fetch(PDO::FETCH_OBJ)) {
					if(!$app->Status)$status = '<span class="label label-warning label-transparent">OPENED</span>';
					else if($app->Status == 1)$status = '<span class="label label-success label-transparent">CLOSED & ACCEPTED</span>';
					else $status = '<span class="label label-default label-transparent">CLOSED & REJECTED</span>';
					echo '
					<tr>
						<td>
							<ul class="list-unstyled list-contacts"><div class="media">
								<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.Config::getData("users","Model",$app->UserID).'.png" class="picture" alt="" style="border: 2px solid #79afbe">
								<span class="status '.(Config::getData("users","Status",$app->UserID) ? 'online' : '').'"></span>
							</div></ul>
						</td>
						<td>'.Config::formatName($app->UserName).'</td>
						<td>'.Config::justFactionName($app->FactionID).'</td>
						<td>'.$status.'</td>
						<td>'.Config::timeAgo($app->Date).'<br><i>'.$app->Date.'</i></td>
						<td><a href="'.Config::$_PAGE_URL.'group/applications/view/'.$app->id.'">View</a></td>
					</tr>
					';
				}
				?>
				</tbody>
			</table><br>
			<?php
			Config::create(Config::rows("wcode_applications","id"));
		} else if(Config::$_url[3] == "create") {
			$factions = $wcs->fetch(PDO::FETCH_OBJ);
			
			if(!Config::isLogged()) { Config::gotoPage("factions"); Config::createSN("danger","You are not logged in!"); }
			else if(Config::isLogged() && $factions->MinLevel > Config::getData("users","Level",Config::getUser()))  { Config::gotoPage("factions"); Config::createSN("warning","Your level is under the requirments!"); }
			else if($factions->Application) { Config::gotoPage("factions"); Config::createSN("danger","Applications for this faction are closed!"); }
			else if(Config::getData("users","Member",Config::getUser())) Config::gotoPage("factions",0,"warning","You already belong to a faction!");
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
				if($checked != $_SESSION['questions']) { Config::gotoPage("group/applications/".Config::$_url[2]."/create",0,"warning","Please complete all the questions!"); }
				else {
					$wcd = Config::$g_con->prepare('INSERT INTO `wcode_applications` (`UserID`,`UserName`,`FactionID`,`Answers`,`Questions`) VALUES (?,?,?,?,?)');
					$wcd->execute(array(Config::getUser(),Config::getData("users","name",Config::getUser()),Config::$_url[2],$questions,$_SESSION['questions']));
					Config::gotoPage("group/applications/".Config::$_url[2]."/list",0,"success","Your application has been posted with success!");
					$_SESSION['questions'] = -1;
				}
			} else {
				$wcd = Config::$g_con->prepare('SELECT `id` FROM `wcode_applications` WHERE `UserID` = ? AND `Status` = 0');
				$wcd->execute(array(Config::getUser()));
				if($wcd->rowCount() >= 2) { Config::gotoPage("factions"); Config::createSN("danger","You can't open more than 2 applications in same time!"); }
				echo '<h5>Create an application for faction <i>'.Config::justFactionName(Config::$_url[2]).'</i></h5>
				<div class="alert alert-info alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<i class="fa fa-info-circle"></i> WE REMIND THAT YOU CANNOT CREATE MORE THAN 2 ALREADY OPENED APPLICATIONS!
				</div><form method="post">';
				$w = Config::$g_con->prepare("SELECT * FROM `wcode_questions` WHERE `factionid` = ?");
				$w->execute(array(Config::$_url[2]));
				$count = 1;
				while($question = $w->fetch(PDO::FETCH_OBJ)) {
					echo '
						<div class="input-group" style="margin-bottom: 8px">
							<span class="input-group-addon">#'.$count.'</span>
							<input type="hidden" name="ques'.$count.'" value="'.$question->question.'">
							<input type="text" class="form-control" placeholder="'.$question->question.'" name="question'.$count.'">
						</div>
					';
					$_SESSION['questions'] = $count;
					$count++;
				}
				echo '
				<center>
					<button type="submit" class="btn btn-info" name="app_send"><i class="fa fa-rocket"></i>
						<span>Send</span>
					</button>
				</center>
				</form>';
			}
		}
	}
} else { Config::gotoPage("factions"); Config::createSN("warning","Oops, something went wrong."); }
?>
</div>
<script>
$(function()
{
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
});
</script>
</div>
</div>
</section>
