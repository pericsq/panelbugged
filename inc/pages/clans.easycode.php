<section>

<?php
if(isset(Config::$_url[1]))
{
	$c = Config::$g_con->prepare("SELECT * FROM `clans` WHERE `ID` = ?");
	$c->execute(array(Config::$_url[1]));
	if(!$c->rowCount()) { Config::gotoPage("clans"); Config::createSN("warning","We couldn't find any clan ID like the one u provide."); } 
	$clan = $c->fetch(PDO::FETCH_OBJ);
	if(Config::isLogged() && Config::getData("users","Clan",Config::getUser()) == $clan->ID && Config::getData("users","ClanRank",Config::getUser()) > 5)
	{
		if(isset($_POST['remove'])) {
			$s = Config::$g_con->prepare("UPDATE `users` SET `Clan` = 0, `ClanRank` = 0, `ClanWarns` = 0, `ClanDays` = 0 WHERE `name` = ?");
			$s->execute(array($_POST['remove']));
			
			$log = 'Clan leader '.Config::getData("users","name",Config::getUser()).' removed player '.$_POST['remove'].' from clan '.$clan->Name.'(#'.$clan->ID.').';
			Config::insertLog(Config::getUser(),Config::getData("users","name",Config::getUser()),$log,Config::getID($_POST['remove']),$_POST['remove']);
			
			Config::gotoPage('clans/'.Config::$_url[1].'',0,"success",'Member <i>'.$_POST['remove'].'</i> has been removed from clan with success!');
		}
	}
	$jj = Config::$g_con->prepare("SELECT `id`,`name`,`Status`,`ClanRank`,`lastOn`,`Model` FROM `users` WHERE `Clan` = ? ORDER BY `ClanRank` DESC");
	$jj->execute(array($clan->ID));
	?>
	
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Clan Info</b>
                </div>
                <div class="panel-body">
                    <b>Clan Name:</b> <?php echo $clan->Name ?><br>
					<b>Clan tag:</b> <font color="#660099"><?php echo '<b><font color="#'.$clan->Color.'">'.$clan->Tag.'</font></b>' ?></font><br>
                    <b>Clan Members:</b> <?php echo $jj->rowCount() ?><br>
                    <b>Clan Deposit:</b> <font color="green">$<?php echo $clan->Safebox ?></font>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Clan Forum</b>
                </div>
                <div class="panel-body">
                    <a href="https://last-times.ro/" target="_blank" class="btn btn-block btn-primary">
                        acceseaza forumul clanului
                    </a>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Clan Description</b>
                </div>
                <div class="panel-body">
                <span><?php echo $clan->ClanMotd ?></span>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Clan Ranks</b>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
							<tr>
							<td>Rank 6</td>
							<td>
							<span class="spanedit6">
							{<?php echo $clan->RankColor6 ?>} » <?php echo '<b><font color="#'.$clan->RankColor6.'">'.$clan->RankName6.'</font></b>' ?></span> 
							</td>
							</tr>
							<tr>
							<td>Rank 5</td>
							<td>
							<span class="spanedit5">
							{<?php echo $clan->RankColor5 ?>} » <?php echo '<b><font color="#'.$clan->RankColor5.'">'.$clan->RankName5.'</font></b>' ?></span> 
							</td>
							</tr>
							<tr>
							<td>Rank 4</td>
							<td>
							<span class="spanedit4">
							{<?php echo $clan->RankColor4 ?>} » <?php echo '<b><font color="#'.$clan->RankColor4.'">'.$clan->RankName4.'</font></b>' ?></span> 
							</td>
							</tr>
							<tr>
							<td>Rank 3</td>
							<td>
							<span class="spanedit3">
							{<?php echo $clan->RankColor3 ?>} » <?php echo '<b><font color="#'.$clan->RankColor3.'">'.$clan->RankName3.'</font></b>' ?></span> 
							</td>
							</tr>
							<tr>
							<td>Rank 2</td>
							<td>
							<span class="spanedit2">
							{<?php echo $clan->RankColor2 ?>} » <?php echo '<b><font color="#'.$clan->RankColor2.'">'.$clan->RankName2.'</font></b>' ?></span> 
							</td>
							</tr>
							<tr>
							<td>Rank 1</td>
							<td>
							<span class="spanedit1">
							{<?php echo $clan->RankColor1 ?>} » <?php echo '<b><font color="#'.$clan->RankColor1.'">'.$clan->RankName1.'</font></b>' ?></span> 
							</td>
							</tr>
							</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Clan Members</b>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
					<table class="table table-minimal">
						<thead>
							<tr>
								<th>Username</th>
								<th>Rank</th>
								<th>Last Login</th>
								<th>Last Login</th>
								<?php
								if(Config::isLogged() && Config::getData("users","Clan",Config::getUser()) == $clan->ID && Config::getData("users","ClanRank",Config::getUser()) > 5) echo '<th>ACTION</th>';
								?>
							</tr>
						</thead>
						<tbody>
						<?php
						while($user = $jj->fetch(PDO::FETCH_OBJ)) {
							echo '
							<tr>
								<td>'.Config::formatName($user->name).'</td>
								<td><b><font color="#'.$clan->RankColor1.'">'.$user->ClanRank.'</font></b></td>
								<td>'.Config::timeAgo($user->lastOn).'</td>
								<td><a href="'.Config::$_PAGE_URL.'complaints/create/'.$user->name.'" class="btn btn-danger btn-sm btn-block"><i class="fa fa-exclamation fa-fw"></i>Report</a></td>';
								if(Config::isLogged() && Config::getData("users","Clan",Config::getUser()) == $clan->ID && Config::getData("users","ClanRank",Config::getUser()) > 5)
								{
									echo '<form method="post">
									<td><button type="submit" name="remove" value="'.$user->name.'" class="btn btn-danger btn-xs">Remove</button></td>
									</form>';
								}
								
							echo '</tr>
							';
						}
						?>
						</tbody>
					</table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
		<div class="panel panel-default">
                <div class="panel-heading">
                    <b>Clan Vehicles</b>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Model</th>
                                    <th>Colours</th>
                                    <th>Map</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
							</table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
	<?php
} else {
	$w = Config::$g_con->prepare("SELECT * FROM `clans` ".Config::limit()."");
	$w->execute();
	echo '
	<div class="page-content">
		<div class="row-fluid">
			<div class="span12">
			<div class="row">
				<div class="col-lg-8">
					<div class="card  bg-purple text-white">
						<div class="card-body">
							<p class="card-title typography-headline">
							Clans
							</p>
								</div>
								</div>
								
								<table class="table table-striped table-bordered table-dark table-responsive-sm">
								<thead>
								<thead>
									<tr>
									<th class="center">ID</th>
									<th>Name - Tag</th>
									<th class="hidden-100">Points</th>
									<th class="hidden-100">Level</th>
									<th class="hidden-480">Members</th>
									</tr>
								</thead>
								<tbody>';
								while($clan = $w->fetch(PDO::FETCH_OBJ)) {
									$uu = Config::$g_con->prepare("SELECT `id` FROM `users` WHERE `Clan` = ?");
									$uu->execute(array($clan->ID));
									$jj = Config::$g_con->prepare("SELECT `id`,`name`,`Status`,`ClanRank`,`lastOn`,`Model` FROM `users` WHERE `Clan` = ? ORDER BY `ClanRank` DESC");
									$jj->execute(array($clan->ID));
									echo '
									<tr>
										<td>'.$clan->ID.'</td>
										<td><a href="'.Config::$_PAGE_URL.'clans/'.$clan->ID.'">'.$clan->Name.'</a><b> <font color="#'.$clan->Color.'">'.$clan->Tag.'</font></b></td>
										<td>'.$clan->ClanPoints.'</td>
										<td>'.$uu->rowCount().'</td>
										<td> '.$jj->rowCount().'/'.$clan->Slots.'</td>
									</tr>
									';
								}
									echo '
									
								</tbody>
								
                            </table>
							</div>
							<div class="col-lg-4">
							<div class="card  bg-purple text-white">
							<div class="card-body">
							<p class="card-title typography-headline">
							Clan Options
							</p>
							</div>
							</div>
							<div class="card bg-dark-2 text-white">
							<div class="card-body">
							<div class="card-text">
							<a href="registerclan" class="btn btn-info">Create a clan</a>
							</div>
							</div>
							</div>
						</div>
                    </div>
					
                </div>
				
        </div>
		
    </div>
	
</section>


';

}
?>	