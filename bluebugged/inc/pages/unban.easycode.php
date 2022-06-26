<div class="panel" style="padding: 10px">
<?php
if(!Config::isLogged()) Config::gotoPage("", 0, "danger", "You must be logged in to can access the page!");
if(!isset(Config::$_url[1])) {
	?>
		<a href="<?php Config::$_PAGE_URL ?>unban/create"><button type="button" class="btn btn-success" title="Create"><i class="ti-pencil"></i>Create
		</button></a><br><br>
		<table class="table table-minimal">
			<thead>
				<tr>
					<th>#</th>
					<th>USER</th>
					<th>TITLE</th>
					<th>STATUS</th>
					<th>DATE</th>
					<th>VIEW</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(Config::getData("users","Admin",Config::getUser())) { $wcs = Config::$g_con->prepare('SELECT `ID`,`Title`,`Status`,`Date`,`UserName`,`ActionBy` FROM `wcode_unban` ORDER BY `Date` DESC'); $wcs->execute(array()); }
			else { $wcs = Config::$g_con->prepare('SELECT `ID`,`Title`,`Status`,`Date`,`ActionBy` FROM `wcode_unban` WHERE `UserID` = ? ORDER BY `Date` DESC '.Config::limit().''); $wcs->execute(array(Config::getUser())); }
			while($unban = $wcs->fetch(PDO::FETCH_OBJ)) {
				echo '
					<tr>
						<td>'.$unban->ID.'</td>
						<td>'.Config::formatName($unban->UserName).'</td>
						<td><a href="'.Config::$_PAGE_URL.'unban/view/'.$unban->ID.'">'.$unban->Title.'</a></td>
						<td>'.(!$unban->Status ? '<span class="label label-success label-transparent">OPENED</span>' : '<span class="label label-danger label-transparent">CLOSED ('.$unban->ActionBy.')</span>').'</td>
						<td>'.$unban->Date.'</td>
						<td><a href="'.Config::$_PAGE_URL.'unban/view/'.$unban->ID.'">View</a></td>
					</tr>
				';
			}
			?>
			</tbody>
		</table><br>
	<?php
	 echo Config::create(Config::rows("wcode_unban","ID"));
} else if(Config::$_url[1] == "view" && isset(Config::$_url[2]) && is_numeric(Config::$_url[2])) {
	$wcs = Config::$g_con->prepare('SELECT `ActionBy`,`Details`,`UserID`,`ID`,`UserName`,`Date`,`Title`,`Status`,`BanDetails` FROM `wcode_unban` WHERE `ID` = ?');
	$wcs->execute(array(Config::$_url[2]));
	if(!$wcs->rowCount()) Config::gotoPage("unban",0,"danger","We couldn't find any unban request with this ID!");
	$unban = $wcs->fetch(PDO::FETCH_OBJ);
	if($unban->UserID != Config::getUser() && !Config::getData("users","Admin",Config::getUser())) Config::gotoPage("unban",0,"danger","You have no rights to access the page!");
	?>
	<div class="panel" style="padding-left: 5px; padding-right: 5px; background-color: #41555e; border: 1px solid #5e7986; border-radius: 5px; margin-bottom: 10px;">
		<div class="pull-right"><form method="post">
			<h5>
				Unban request(#<?php echo $unban->ID ?>) created by <?php echo Config::formatName($unban->UserName) ?>
				<?php
				echo '
				<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.Config::getData("users","Model",$unban->UserID).'.png" class="picture" alt="" style="border: 2px solid #79afbe; height: 17px; border-radius: 10px;">
				';
				?>
			</h5>
		</div>
		<h5><?php echo Config::xss(Config::clean($unban->Details));
			if($unban->UserID == Config::getUser()) {
				echo ' <button type="submit" name="delete" class="btn btn-default btn-xs">DELETE</button>';
				if(isset($_POST['delete'])) {
					$wcs = Config::$g_con->prepare('DELETE FROM `wcode_unban` WHERE `id` = ?');
					$wcs->execute(array(Config::$_url[2]));
					Config::gotoPage("unban",0,"success","Request has been deleted with success!");
				}
			}
			if(Config::getData("users","Admin",Config::getUser())) { 
				if($unban->Status) echo ' <button type="submit" name="open" class="btn btn-success btn-xs">OPEN</button>';
				else echo ' <button type="submit" name="close" class="btn btn-danger btn-xs">CLOSE</button> <button type="submit" name="unban" class="btn btn-info btn-xs">UNBAN</button>';
				if(isset($_POST['open'])) {
					$wcs = Config::$g_con->prepare('UPDATE `wcode_unban` SET `Status` = 0 WHERE `id` = ?');
					$wcs->execute(array(Config::$_url[2]));
					Config::gotoPage("unban/view/".Config::$_url[2]."",0,"success","Request has been <b>opened</b> with success!");
				}
				if(isset($_POST['close'])) {
					$wcs = Config::$g_con->prepare('UPDATE `wcode_unban` SET `Status` = 1 WHERE `id` = ?');
					$wcs->execute(array(Config::$_url[2]));
					Config::gotoPage("unban/view/".Config::$_url[2]."",0,"success","Request has been just <b>closed</b>! Player is still banned.");
				}
				if(isset($_POST['unban'])) {
					$wcs = Config::$g_con->prepare('DELETE FROM `bans` WHERE `PlayerName` = ?');
					$wcs->execute(array($unban->UserName));
					$wcs = Config::$g_con->prepare('UPDATE `wcode_unban` SET `Status` = 1,`ActionBy` = "Unbanned" WHERE `id` = ?');
					$wcs->execute(array(Config::$_url[2]));
					Config::gotoPage("unban/view/".Config::$_url[2]."",0,"success","Request has been <b>closed</b> and player has been unbanned!");
				}
			}
		?> 
		</h5>
		</form>
	</div>
	<div class="col-md-3">
		<div class="panel" style="padding: 10px; margin-bottom: 0px;">
			<?php
			$getban = explode("|",$unban->BanDetails);
			echo $getban[0] . '<br>' . $getban[1] . '<br>' . $getban[2] . '<br>' . $getban[3] . '<br>' ;
			?>
		</div>
	</div>
	<div class="col-md-9">
		<?php echo Config::xss(Config::clean($unban->Details)) ?>
	</div>
	<div class="clearfix"></div>
	<div class="panel" style="padding-left: 5px; padding-right: 5px; background-color: #41555e; border: 1px solid #5e7986; border-radius: 5px; margin-bottom: 10px; margin-top: 10px;">
		<div class="pull-right">
			<h5>
				<small><?php echo $unban->Date . ' (' . Config::timeAgo($unban->Date). ')' ?></small>
			</h5>
		</div>
		<h5>
			<span class="label label-default label-transparent"><?php echo $unban->ActionBy ?></span>
			<?php echo (!$unban->Status ? '<span class="label label-success label-transparent">OPENED</span>' : '<span class="label label-danger label-transparent">CLOSED</span>') ?>
		</h5>
	</div>
	
	<ul class="list-unstyled list-contacts">
	<?php
	$wcjd = Config::$g_con->prepare('SELECT * FROM `wcode_commentaries` WHERE `Section` = "unban" AND `TopicID` = ? ORDER BY `ID` ASC');
	$wcjd->execute(array(Config::$_url[2]));
	if(!$wcjd->rowCount()) echo '<br><center><i><span class="label label-default label-transparent">Nobody responded yet to your unban request.</span></i></center><br>';
	else {
		while($comment = $wcjd->fetch(PDO::FETCH_OBJ)) {
			echo '
			<li>
				<div class="media">
					<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.$comment->Skin.'.png" class="picture" alt="" style="border: 2px solid #79afbe">
					<span class="status '.(Config::getData("users","Status",$comment->UserID) ? 'online' : '').'"></span>
				</div>
				<div class="info" style="float: none; padding-left: 50px"><form method="post">
					<span class="name"><i><b>'.Config::formatName($comment->UserName).'</b></i>';
					if($comment->UserID == $unban->UserID) echo ' <span class="label label-primary label-transparent" style="border-radius: 10px;">Author</span>';
					else if(Config::getData("users","Admin",$comment->UserID))  echo ' <span class="label label-danger label-transparent" style="border-radius: 10px;">ADMIN</span>';
					else echo ' <span class="label label-default label-transparent" style="border-radius: 10px;">Unknown</span>';
					echo '<i><small> replayed:</small></i><br>'.Config::xss(Config::clean($comment->Text)).'</span>
					<span class="email">'.Config::timeAgo($comment->Date).'';
					if(Config::isAdmin(Config::getUser())) {
						echo ' <button type="submit" class="btn btn-link btn-xs" title="Delete" name="delete_com" value="'.$comment->ID.'"><i class="fa fa-trash" style="color: red"></i><span class="sr-only">Delete</span></button>';
					}
					echo '</span>
				</form></div>';
			echo '</li>';
		}
	}
	if($unban->Status == 0) {
		if(isset($_POST['delete_com']) && Config::getData("users","Admin",Config::getUser())) {
			$wcjd = Config::$g_con->prepare('DELETE FROM `wcode_commentaries` WHERE `ID` = ?');
			$wcjd->execute(array($_POST['delete_com']));
			Config::gotoPage('unban/view/'.Config::$_url[2].'',0,"success","You deleted with success the comment!");
		}
		if(isset($_POST['comment_send']) && strlen($_POST['text_comm'])) {
			$wcjd = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"unban", ?)');
			$wcjd->execute(array(Config::getUser(),Config::getData("users","Name",Config::getUser()),Config::getData("users","Model",Config::getUser()),$_POST['text_comm'],Config::$_url[2]));
			Config::gotoPage('unban/view/'.Config::$_url[2].'',0,"success","Your comment has been published!");
			
			if($unban->UserID != Config::getUser()) {
				$notif = 'New comment has been posted in your unban request!';
				$link = Config::$_PAGE_URL.'unban/view/' . Config::$_url[2];
				Config::makeNotification($unban->UserID,$unban->UserName,$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);
			}
		}
		?>	
			<form method="post">
				<div class="input-group">
					<input class="form-control" placeholder="Write your comment" name="text_comm" type="text">
					<span class="input-group-btn">
						<button class="btn btn-primary" name="comment_send" type="submit">Post</button>
					</span>
				</div>
			</form>
		<?php
	} else { ?>
			<div class="input-group">
				<input class="form-control" placeholder="Commentaries has been disabled" type="text" disabled>
				<span class="input-group-btn">
					<button class="btn btn-primary" type="submit" disabled>Post</button>
				</span>
			</div>
		<?php 
	}
	?>
	</ul>
	
	<?php
} else if(Config::$_url[1] == "create") {
	
?>
<div class="col-md-3">
	<div class="panel" style="padding: 10px; background-color: #4c626d;">
		<?php
		$wcjd = Config::$g_con->prepare('SELECT * FROM `bans` WHERE `PlayerName` = ?');
		$wcjd->execute(array(Config::getData("users","name",Config::getUser())));
		if($wcjd->rowCount()) {
			$get = $wcjd->fetch(PDO::FETCH_OBJ);
			$details = 'Banned by: '.$get->AdminName.'|Reason: '.$get->Reason.'|Date: '.$get->BanTimeDate.'|Banned: '.($get->Days ? 'for '.$get->Days.' days' : '<i>Permanent</i>').'';
			if(isset($_POST['submit']) && strlen($_POST['unban']) && strlen($_POST['title']) < 33) {
				$wc = Config::$g_con->prepare('INSERT INTO `wcode_unban` (`UserID`,`UserName`,`Title`,`Details`,`BanDetails`) VALUES (?,?,?,?,?)');
				$wc->execute(array(Config::getUser(),Config::getData("users","name",Config::getUser()),$_POST['title'],$_POST['unban'],$details));
				Config::gotoPage("unban",0,"success","Your unban request has been posted, soon an admin will take a look of your case!");
			}
			echo '
			<p><b>Banned by:</b> '.Config::formatName($get->AdminName).'</p>
			<p><b>Reason:</b> '.$get->Reason.'</p>
			<p><b>Date:</b> '.$get->BanTimeDate.'</p>
			<p><b>Banned:</b> '.($get->Days ? 'for '.$get->Days.' days' : '<i>Permanent</i>').'</p>
			';
		} else Config::gotoPage("unban",0,"danger","Contact an administrator because your ban details cannot be displayed or <u>you dont have a ban</u>!");
		?>
	</div>
</div>
<div class="col-md-9">
	<form method="post">
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-pencil"></i></span>
			<input class="form-control" placeholder="Title (max. 32char)" type="text" name="title" required>
		</div><br>
		<div class="form-group">
			<label for="contact-message" class="control-label sr-only">Message</label>
			<input placeholder="Enter your text" class="form-control" type="text" name="unban" style="width: 100%">
			<br>
			<small><i class="fa fa-info-circle"></i> Write with complex of details including even photos or videos!</small>
		</div>
		<center><button type="submit" name="submit" class="btn btn-info">Create</button></center>
	</form>
</div>
<div class="clearfix"></div>
<?php
}
?>
</div>