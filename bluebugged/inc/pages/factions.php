<?php
if(isset(Config::$_url[1])) echo '';
	
if(!isset(Config::$_url[1])) {
		
		$wcs = Config::$g_con->prepare('SELECT `ID`,`Name`,`MinLevel`,`App` FROM `factions`');
		$wcs->execute();
		echo '<div class="wrapper">
			    <div class="container-fluid">
			        <div class="row">';
		while($factions = $wcs->fetch(PDO::FETCH_OBJ)) {
			$jj = Config::$g_con->prepare('SELECT `ID` FROM `users` WHERE `Member` = ?'); $jj->execute(array($factions->ID));
			$app = Config::$g_con->prepare('SELECT `ID` FROM `wcode_applications` WHERE `FactionID` = ? AND `Status` = 0'); $app->execute(array($factions->ID));
			$compl = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Faction` = ? AND `Status` = 0'); $compl->execute(array($factions->ID));
		  echo '
		  		<div class="col-lg-3 col-md-3 mt-5">
                    <div class="card card-bordered">
                        <div class="card-body">
					    <center>
						    <div class="title">
					          	<h3><strong>'.$factions->Name.'</strong></h3>
					        </div>	
							<div class="contact-card-info">
							<hr>
								<a href="'.Config::$_PAGE_URL.'factions/members/'.$factions->ID.'"><i class="btn btn-success"> members</a></i>
								<a href="'.Config::$_PAGE_URL.'factions/applications/'.$factions->ID.'/list"><i class="btn btn-primary">Applicatii</a></i>
							</div>
							<hr>
							<div class="contact-card-button">';						
									if(!Config::isLogged()) echo '<button class="btn btn-danger mb-3">You must be logged in to an account</button>';
									else if($factions->Application) echo '<button class="btn btn-secondary mb-3">Application closed</button>';
									else if(Config::getData("users","Member",Config::getUser())) echo '<button class="btn btn-danger mb-3">You are in a faction</button>';
									else if(Config::isLogged() && $factions->MinLevel > Config::getData("users","Level",Config::getUser())) echo '<button class="btn btn-danger mb-3">Level '.$factions->MinLevel.' necesary</button>';
									else if(!$factions->Application) echo '<a class="btn btn-primary mb-3" href="'.Config::$_PAGE_URL.'factions/applications/'.$factions->ID.'/create">Apply in faction</a>';
									else echo '<button class="btn btn-danger mb-3">You are not authorized to do anything</button>';					
				   echo '
							</div>
				   		</center>
				   		</div>
				   		
					</div>
				</div>';
			
		}
		echo '</div>
            </div>
        </div>';

} else if(Config::$_url[1] == "members" && isset(Config::$_url[2])) {
	$wcs = Config::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
	$wcs->execute(array(Config::$_url[2]));
	if(!$wcs->rowCount()) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
	if(Config::$_url[2] == 0) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
	$users = Config::$g_con->prepare('SELECT `ID`,`name`,`Rank`,`FWarn`,`lastOn`,`CChar`,`Warns` FROM `users` WHERE `Member` = ? OR `Leader` = ? ORDER BY `Rank` DESC');
	$users->execute(array(Config::$_url[2],Config::$_url[2]));
	echo '
<section class="no-padding-top">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
	        <div class="block margin-bottom-sm">
	          <div class="title"><strong>View members <small>('.Config::justFactionName((Config::$_url[2])).')</small></strong></div>
	          <div class="table-responsive"> 
	            <table class="table">
	              <thead>
	                <tr>
					<th>#</th>
					<th>user</th>
					<th>fwarns</th>
					<th>warns</th>
					<th>grad</th>
					<th>last login</th>
	                </tr>
	              </thead>
	          	<tbody>';
			while($user = $users->fetch(PDO::FETCH_OBJ))
			{
				echo '
				<tr>
					<td>
						<ul class="list-unstyled list-contacts"><div class="media">
							<img src="'.Config::$_PAGE_URL.'template/avatars/'.$user->CChar.'.png" width="50" height="50" class="img-fluid rounded-circle" alt="">
						</div></ul>
					</td>
					<td>'.Config::formatName($user->name).'</td>
					<td>'.$user->FWarn.'/3</td>
					<td>'.$user->Warns.'/3</td>
					<td>Rank '.$user->Rank.'</td>
					<td>'.$user->lastOn.'</td>
				</tr>
				';
			}
			echo '
			       </tbody>
	            </table>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
    </div>
  </div>
</section>';
} else if(Config::$_url[1] == "applications" && isset(Config::$_url[2]) && isset(Config::$_url[3])) {
	if(!Config::isLogged()) { Config::gotoPage("factions"); Config::createSN("danger","Nu esti logat!"); }
	$wcs = Config::$g_con->prepare('SELECT * FROM `factions` WHERE `ID` = ?');
	$wcs->execute(array(Config::$_url[2]));
	if(Config::$_url[2] == "view" && isset(Config::$_url[3]))
	{
		$wcj = Config::$g_con->prepare('SELECT * FROM `wcode_applications` WHERE `id` = ?');
		$wcj->execute(array(Config::$_url[3]));
		if(!$wcj->rowCount()) { Config::gotoPage("factions"); Config::createSN("danger","We couldn't find any application!"); }
		$app = $wcj->fetch(PDO::FETCH_OBJ);
		
		$wcjd = Config::$g_con->prepare('SELECT `ID`,`user`,`Skin`,`Warns`,`lastLogin`,`Level`,`ContractTime`,`Rank` FROM `users` WHERE `ID` = ?');
		$wcjd->execute(array($app->UserID));
		$user = $wcjd->fetch(PDO::FETCH_OBJ);
		
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
						$link = Config::$_PAGE_URL.'factions/applications/view/';
						Config::makeNotification($app->UserID,$app->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
					}
					if(isset($_POST['rejected']))
					{
						$vv = Config::$g_con->prepare('UPDATE `wcode_applications` SET `Status` = "2",`ActionBy` = ? WHERE `id` = ?');
						$vv->execute(array(Config::getNameFromID(Config::getUser()),Config::$_url[3])); $app->Status = 2;
						
						$notif = 'Your faction application has been rejected!';
						$link = Config::$_PAGE_URL.'factions/applications/view/' . Config::$_url[3];
						Config::makeNotification($app->UserID,$app->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
					}
				}
			}
		}
		if(!$app->Status) $status = '<button class="btn btn-outline-success">OPENED</button>';
		else if($app->Status == 1) { $status = '<button class="btn btn-outline-warning">ACCEPTED</button>'; $textQ = "Accepted";}
		else { $status = '<button class="btn btn-outline-danger">REJECTED</button>'; $textQ = "Rejected"; }
		if($app->Status != 0) $statusss = '<p>'.$textQ.' by '.Config::formatName($app->ActionBy).'</p>';
		else $statusss = '';
		echo '
<div class="wrapper">
  <div class="container-fluid">
	<div class="row">
      <div class="col-lg-4">
		<div class="card">
		  <div class="card-body">
	        <div class="block">
	        <div class="title"><center><strong>Creator details</strong></center></div>
	          <div class="user-block">
	          <img src="'.Config::$_PAGE_URL.'template/avatars/'.$user->Skin.'.png" class="rounded-circle avatar user-thumb" height="100" alt="user">
				<center>
	              <h3><strong>'.Config::formatName($user->user).'</strong></h3><br>
	              '.$status.'
	              '.$statusss.'
	            </center>
	          </div>
	          <hr>
			  <p><div class="pull-right"><b>'.$user->lastLogin.'</b></div>Last login</p>
			  <p><div class="pull-right"><b>'.$user->Level.'</b></div>Level</p>
			  <p><div class="pull-right"><b>'.$user->ContractTime.'</b></div>Hours played</p>
			  <p><div class="pull-right"><b>'.$user->Warns.'/3</b></div>Warnings</p>
			  <hr>
			  <p>'.$app->Date.'</p>
			  <br>';
				if(Config::isLogged())
				{
					if(Config::getData("users","Member",Config::getUser()) == $app->FactionID && Config::getData("users","Rank",Config::getUser()) >= Config::$_LEADER_RANK) 
					{
						if($app->Status == 0)
						{
							echo '
							<form method="post">
								<button type="submit" class="btn btn-success btn-xs mb-3" name="accepted" value="1">ACCEPTED</button>
								<button type="submit" class="btn btn-danger btn-xs mb-3" name="rejected" value="2">REJECTED</button>
							</form>';
						}
					}
				}
				echo '
	        </div>
	      </div>
	    </div>
	  </div>
      <div class="col-lg-8">
		<div class="card">
		  <div class="card-body">
	        <div class="block margin-bottom-sm">
	        <div class="title">
	          <strong>The answer to the test test:  </strong>
			</div>
	          <div class="table-responsive"> 
	            <table class="table">
	              <tbody>';
					$row = explode("|", $app->Answers);
					for ($x = 0; $x < $app->Questions; $x++) {
						$show = explode("::", $row[$x]);
						echo'
	                <tr>
	                  <td><strong>'.Config::clean($show[0]).':</strong><br>'.Config::clean($show[1]).'</td>
	                </tr>';
						}
					echo '
	              </tbody>
	            </table>
	          </div>
	        </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>';
	} else {
		if(!$wcs->rowCount()) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
		if(Config::$_url[2] == 0) { Config::gotoPage("factions"); Config::createSN("warning","There's no faction with that number #ID!"); }
		if(Config::$_url[3] == "list") {
			$wcd = Config::$g_con->prepare('SELECT * FROM `wcode_applications` WHERE `FactionID` = ? ORDER BY `id` DESC '.Config::limit().'');
			$wcd->execute(array(Config::$_url[2]));
			echo '
			<div class="wrapper">
			  <div class="container-fluid">
			    <div class="row">
			      <div class="col-lg-12">
			        <div class="card">
			          <div class="card-body">
			            <div class="block margin-bottom-sm">
			              <div class="table-responsive"> 
			                <table class="table">
							<thead>
								<tr>
									<th>faction</th>
									<th>status</th>
									<th>date</th>
									<th>action</th>
								</tr>
							</thead>
							<tbody>';
							while($app = $wcd->fetch(PDO::FETCH_OBJ)) {
								if(!$app->Status)$status = '<button class="btn btn-success mb-3">OPENED</button>';
								else if($app->Status == 1)$status = '<button class="btn btn-warning mb-3">ACCEPTED</button>';
								else $status = '<button class="btn btn-danger mb-3">REJECTED</button>';
								echo '
								<tr>
									<td>'.Config::formatName($app->UserName).'</td>
									<td>'.Config::justFactionName($app->FactionID).'</td>
									<td>'.$status.'</td>
									<td>'.$app->Date.'</td>
									<td><a href="'.Config::$_PAGE_URL.'factions/applications/view/'.$app->id.'">View</a></td>
								</tr>
								';
							}
							echo '
							</tbody>
			                </table>
			              </div>
			            </div>
			          </div>
			        </div>
			      </div>
			    </div>
			  </div>
			</div>
			<br>';
			Config::create(Config::rows("wcode_applications","id"));
		} else if(Config::$_url[3] == "create") {
			$factions = $wcs->fetch(PDO::FETCH_OBJ);
			
			if(!Config::isLogged()) { Config::gotoPage("factions"); Config::createSN("danger","Nu esti logat!"); }
			else if(Config::isLogged() && $factions->MinLevel > Config::getData("users","Level",Config::getUser()))  { Config::gotoPage("factions"); Config::createSN("warning","Nu ai level-ul necesar!"); }
			else if($factions->Application) { Config::gotoPage("factions"); Config::createSN("danger","Aplicatiile sunt inchise!"); }
			else if(Config::getData("users","Member",Config::getUser())) Config::gotoPage("factions",0,"warning","Esti deja intr-o factiune!");
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
				if($checked != $_SESSION['questions']) { Config::gotoPage("faction/applications/".Config::$_url[2]."/create",0,"warning","Please complete all the questions!"); }
				else {
					$wcd = Config::$g_con->prepare('INSERT INTO `wcode_applications` (`UserID`,`UserName`,`FactionID`,`Answers`,`Questions`) VALUES (?,?,?,?,?)');
					$wcd->execute(array(Config::getUser(),Config::getData("users","user",Config::getUser()),Config::$_url[2],$questions,$_SESSION['questions']));
					Config::gotoPage("faction/applications/".Config::$_url[2]."/list",0,"success","Your application has been posted with success!");
					$_SESSION['questions'] = -1;
				}
			} else {
				$wcd = Config::$g_con->prepare('SELECT `id` FROM `wcode_applications` WHERE `UserID` = ? AND `Status` = 0');
				$wcd->execute(array(Config::getUser()));
				if($wcd->rowCount() >= 2) { Config::gotoPage("factions"); Config::createSN("danger","You can't open more than 2 applications in same time!"); }
				echo '<section class="no-padding-top">
					<center>
			          <div class="container-fluid">
			            <div class="row">
			              <div class="col-lg-12">
			                <div class="block">
			  				<div class="title"><strong>Create an application for faction <small>('.Config::justFactionName(Config::$_url[2]).')</small></strong></div>
			                  <div class="block-body">
							  </center>
			                    <form class="form-horizontal" method="post">';
				$w = Config::$g_con->prepare("SELECT * FROM `wcode_questions` WHERE `factionid` = ?");
				$w->execute(array(Config::$_url[2]));
				$count = 1;
				while($question = $w->fetch(PDO::FETCH_OBJ)) {
					echo '
					  <div class="form-group row">
                        <div class="col-sm-12">
						  <input type="hidden" name="ques'.$count.'" value="'.$question->question.'">
                          <input type="text" class="form-control" placeholder="'.$count.') '.$question->question.'" name="question'.$count.'">
                        </div>
                      </div>
					';
					$_SESSION['questions'] = $count;
					$count++;
				}
                echo '<center>
					<button type="submit" class="btn btn-outline-primary" name="app_send">Trimite aplicatia</button>
				</center>
				</form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>';
			}
		}
	}
} else { Config::gotoPage("factions"); Config::createSN("warning","Ai gresit pagina."); }
?>
</div>
<script>
$(function()
{
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
});
</script>