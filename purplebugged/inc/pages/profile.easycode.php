<?php
date_default_timezone_set('Europe/Bucharest');
$time = time();
?>

<?php
$w = Config::$g_con->prepare('SELECT * FROM `users` WHERE `name` = ?');
$w->execute(array(Config::$_url[1]));
if (!$w->rowCount()) Config::gotoPage("");
else $profile = $w->fetch(PDO::FETCH_OBJ);

if (Config::isAdmin(Config::getUser())) {
	if (isset($_POST['unban'])) {
		$w = Config::$g_con->prepare('DELETE FROM `bans` WHERE `PlayerName` = ?');
		$w->execute(array($profile->name));
		$notif = 'You\'ve been unbanned by ' . Config::getData("users", "name", Config::getUser()) . '!';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
		Config::gotoPage("profile/" . $profile->name . "", 0, "success", "Player has been unbanned with success!");
	}

	if (isset($_POST['unsuspend'])) {
		$w = Config::$g_con->prepare('DELETE FROM `wcode_suspend` WHERE `User` = ?');
		$w->execute(array($profile->name));
		$notif = 'You\'ve been unsuspended by ' . Config::getData("users", "name", Config::getUser()) . '!';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
		Config::gotoPage("profile/" . $profile->name . "", 0, "success", "Player has been unsuspended with success!");
	}

	if (isset($_POST['s_permanent']) && strlen($_POST['s_res'])) {
		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
			$notif = 'Admin ' . Config::getName(Config::getUser(), false) . ' attempted to suspend you.';
			$link = Config::$_PAGE_URL . 'profile/' . Config::getName(Config::getUser(), false);
			Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
			return;
		}
		$w = Config::$g_con->prepare('INSERT INTO `wcode_suspend` (`User`,`Userid`,`Admin`,`Adminid`,`Date_unix`,`Reason`) VALUES (?,?,?,?,?,?)');
		$w->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getUser(), time(), $_POST['s_res']));

		$log = 'Admin ' . Config::getData("users", "name", Config::getUser()) . ' suspended permanently player ' . $profile->name . ' for ' . $_POST['s_res'] . '.';
		Config::insertLog(Config::getUser(), Config::getData("users", "name", Config::getUser()), $log, $profile->id, $profile->name);

		$notif = 'You\'ve been permanently suspended from panel!';
		$link = Config::$_PAGE_URL . '';
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

		Config::gotoPage('profile/' . $profile->name . '', 0, 'success', 'Player has been suspended from panel permanently!');
	}
	if (isset($_POST['s_temp']) && strlen($_POST['s_res']) && strlen($_POST['s_days']) && is_numeric($_POST['s_days'])) {
		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
			$notif = 'Admin ' . Config::getName(Config::getUser(), false) . ' attempted to suspend temporarly you.';
			$link = Config::$_PAGE_URL . 'profile/' . Config::getName(Config::getUser(), false);
			Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
			return;
		}
		$expire = time() + (86400 * $_POST['s_days']);
		$w = Config::$g_con->prepare('INSERT INTO `wcode_suspend` (`User`,`Userid`,`Admin`,`Adminid`,`Date_unix`,`Reason`,`Days`,`Expire_unix`,`Expire`) VALUES (?,?,?,?,?,?,?,?,?)');
		$w->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getUser(), time(), $_POST['s_res'], $_POST['s_days'], $expire, gmdate("Y-m-d H:i:s", $expire)));

		$log = 'Admin ' . Config::getData("users", "name", Config::getUser()) . ' suspende temporary for ' . $_POST['days'] . ' days, player ' . $profile->name . ' for ' . $_POST['s_res'] . '.';
		Config::insertLog(Config::getUser(), Config::getData("users", "name", Config::getUser()), $log, $profile->id, $profile->name);

		$notif = 'You\'ve been suspended temporary!';
		$link = Config::$_PAGE_URL . 'unban';
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

		Config::gotoPage('profile/' . $profile->name . '', 0, 'success', 'Player has been suspended temporary for ' . $_POST['s_days'] . ' days!');
	}


	if (isset($_POST['submit_action'])) {
		$log = 'Admin ' . Config::getData("users", "name", Config::getUser()) . ' reseted ' . $_POST['submit_action'] . ' field of player ' . $profile->name . '.';
		Config::insertLog(Config::getUser(), Config::getData("users", "name", Config::getUser()), $log, $profile->id, $profile->name);

		$notif = 'You have no more ' . $_POST['submit_action'] . ' points thanks to ' . Config::getName(Config::getUser(), false) . '.';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

		$w = Config::$g_con->prepare('UPDATE `users` SET `' . $_POST['submit_action'] . '` = 0 WHERE `id` = ?');
		$w->execute(array($profile->id));
		$var = intval($_POST['submit_action']);
		$profile->$var = 0;
		echo Config::csSN("success", "Pharameter(" . $_POST['submit_action'] . ") has been reseted with succes!");
	}

	if (isset($_POST['warnup'])) {
		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
			$notif = 'Admin ' . Config::getName(Config::getUser(), false) . ' attempted to warn you up.';
			$link = Config::$_PAGE_URL . 'profile/' . Config::getName(Config::getUser(), false);
			Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
			return;
		}
		$log = 'Admin ' . Config::getData("users", "name", Config::getUser()) . ' gave one Warning Point to player ' . $profile->name . ' for: ' . $_POST['reason'] . '.';
		Config::insertLog(Config::getUser(), Config::getData("users", "name", Config::getUser()), $log, $profile->id, $profile->name);

		$notif = 'You received +1 WarnPoint from ' . Config::getName(Config::getUser(), false) . ' for ' . $_POST['reason'] . '.';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

		$w = Config::$g_con->prepare('UPDATE `users` SET `Warnings` = `Warnings`+1 WHERE `id` = ?');
		$w->execute(array($profile->id));
		if ($profile->Warnings == 2) {
			$notif = 'After acumulating 3/3 points you\'ve been banned for 7 days.';
			$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
			Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

			$w = Config::$g_con->prepare('INSERT INTO `bans` (`PlayerName`,`AdminName`,`Reason`,`IP`) VALUES (?,?,?,?)');
			$w->execute(array($profile->name, "AdmBot", "3/3 warns", $profile->IP));

			$w = Config::$g_con->prepare('UPDATE `users` SET `Warnings` = 0 WHERE `id` = ?');
			$w->execute(array($profile->id));

			Config::gotoPage("profile/" . $profile->name . "", 0, "success", "This player has been banned for acumulating 3/3 warn points. Last reason being: " . $_POST['reason'] . "");
		} else Config::gotoPage("profile/" . $profile->name . "", 0, "success", "Player got a new warn point for reason: " . $_POST['reason'] . ".");
	}
	if (isset($_POST['unmute'])) {
		$w = Config::$g_con->prepare('UPDATE `users` SET `Muted` = 0, `MuteTime` = 0 WHERE `id` = ?');
		$w->execute(array($profile->id));
		$notif = 'You\'ve been unmuted by ' . Config::getData("users", "name", Config::getUser()) . '!';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
		Config::gotoPage("profile/" . $profile->name . "", 0, "success", "Player has been unmuted with success!");
	}
	if (isset($_POST['mute_action']) && strlen($_POST['mute_res'])) {
		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
			$notif = 'Admin ' . Config::getName(Config::getUser(), false) . ' attempted to mute you.';
			$link = Config::$_PAGE_URL . 'profile/' . Config::getName(Config::getUser(), false);
			Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
			return;
		}
		$log = 'Admin ' . Config::getData("users", "name", Config::getUser()) . ' muted(' . $_POST['mute_min'] . ' minutes) player ' . $profile->name . ' for ' . $_POST['mute_res'] . '.';
		Config::insertLog(Config::getUser(), Config::getData("users", "name", Config::getUser()), $log, $profile->id, $profile->name);

		$notif = 'You\'ve been muted for ' . $_POST['mute_min'] . ' minutes!';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

		$w = Config::$g_con->prepare('UPDATE `users` SET `Muted` = 1, `MuteTime` = ? WHERE `id` = ?');
		$w->execute(array($_POST['mute_min'] * 60, $profile->id));
		Config::gotoPage("profile/" . $profile->name . "", 0, "success", "Player have been muted(" . $_POST['mute_min'] . " minutes) for reason: " . $_POST['mute_res'] . ".");
	}
	if (isset($_POST['warndown'])) {
		$log = 'Admin ' . Config::getData("users", "name", Config::getUser()) . ' took one Warning Point to player ' . $profile->name . ' for: ' . $_POST['reason'] . '.';
		Config::insertLog(Config::getUser(), Config::getData("users", "name", Config::getUser()), $log, $profile->id, $profile->name);

		$notif = 'You have one WarnPoint less from ' . Config::getName(Config::getUser(), false) . ' for ' . $_POST['reason'] . '.';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

		$w = Config::$g_con->prepare('UPDATE `users` SET `Warnings` = `Warnings`-1 WHERE `id` = ?');
		$w->execute(array($profile->id));
		Config::gotoPage("profile/" . $profile->name . "", 0, "success", "Player have less one warn point for reason: " . $_POST['reason'] . ".");
	}

	if (isset($_POST['delete_ac'])) {
		$w = Config::$g_con->prepare('DELETE FROM `faction_logs` WHERE `id` = ?');
		$w->execute(array($_POST['delete_ac']));
		$log = 'Admin ' . Config::getData("users", "name", Config::getUser()) . ' deleted log ID #' . $_POST['delete_ac'] . ' from faction history of ' . $profile->name . '.';
		Config::insertLog(Config::getUser(), Config::getData("users", "name", Config::getUser()), $log, $profile->id, $profile->name);
		echo Config::csSN("success", "Faction history line #" . $_POST['delete_ac'] . " has been deleted!");
	}
	if (isset($_POST['mon_submit']) && Config::isAdmin(Config::getUser(), 6)) {
		$notif = 'Your money state(' . Config::formatNumber($_POST['money_n']) . ' / ' . Config::formatNumber($_POST['bank_n']) . ') has been updated by Admin ' . Config::getName(Config::getUser(), false) . '.';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
		$w = Config::$g_con->prepare('UPDATE `users` SET `Money` = ?, `Bank` = ? WHERE `id` = ?');
		$w->execute(array($_POST['money_n'], $_POST['bank_n'], $profile->id));
		$profile->Money = $_POST['money_n'];
		$profile->Bank = $_POST['bank_n'];
		echo Config::csSN("success", "Money got updated with succes!");
	}
	if (isset($_POST['ppr_submit']) && Config::isAdmin(Config::getUser(), 6)) {
		$notif = 'Your premium points(' . $_POST['pointsp'] . ') has been updated by Admin ' . Config::getName(Config::getUser(), false) . '.';
		$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
		Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
		$w = Config::$g_con->prepare('UPDATE `users` SET `GoldPoints` = ? WHERE `id` = ?');
		$w->execute(array($_POST['pointsp'], $profile->id));
		$profile->GoldPoints = $_POST['pointsp'];
		echo Config::csSN("success", "Premium Points has been updated with success!");
	}
}
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
if (Config::isLogged(Config::getUser()) && (Config::isAdmin(Config::getUser()) || $profile->id == Config::getUser())) {
	if (isset($_POST['email_submit'])) {
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$notif = 'Your email has been changed by ' . Config::getName(Config::getUser(), false) . '.';
			$link = Config::$_PAGE_URL . 'profile/' . $profile->name;
			Config::makeNotification($profile->id, $profile->name, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);

			$w = Config::$g_con->prepare('UPDATE `users` SET `Email` = ? WHERE `id` = ?');
			$w->execute(array($_POST['email'], $profile->id));
			$profile->email = $_POST['email'];
			echo Config::csSN("success", "Email has been changed with success!");
		} else echo Config::csSN("danger", "Please insert an valid email form!");
	}
}

if (Config::isLogged() && Config::getData("users", "Admin", Config::getUser())) {
?>
	<div id="givewarn" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<form method="post">
						<center>
							<?php
							if ($profile->Warnings < 3) echo '<button type="submit" class="btn btn-success" title="up" name="warnup"><i class="fa fa-plus"></i><span class="sr-only">up</span></button>';
							if ($profile->Warnings > 0) echo ' <button type="submit" class="btn btn-danger" title="down" name="warndown"><i class="fa fa-minus"></i><span class="sr-only">down</span></button>';
							?>
							<br><br><input class="form-control" placeholder="Reason" type="text" name="reason" required>
						</center>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="suspend" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<h5><i class="fa fa-warning"> </i> Suspend player's access to panel</h5>
					<form method="post">
						<input class="form-control input-sm" placeholder="Reason" type="text" name="s_res" required><br>
						<center>
							<input class="form-control input-sm" placeholder="Days" type="text" style="width: 20%" name="s_days"><br>
							<button type="submit" name="s_permanent" class="btn btn-success btn-xs" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="No need to complete with days!" data-original-title="">Permanent</button>
							<button type="submit" name="s_temp" class="btn btn-primary btn-xs" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Be sure you completed for how many days!" data-original-title="">Temporary</button>
						</center>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="givemute" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<h5><i class="fa fa-legal"> </i> Mute this player</h5>
					<form method="post">
						<input class="form-control input-sm" placeholder="Reason" type="text" name="mute_res" required><br>
						<center>
							<input class="form-control input-sm" placeholder="Time in minutes" type="text" name="mute_min" required><br>
							<button type="submit" name="mute_action" class="btn btn-success btn-xs" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="No need to complete with days!" data-original-title="">Mute</button>
						</center>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="tagadd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<h5><i class="fa fa-code"> </i> Add TAG</h5>
					<form method="post">
						<center><input type="color" value="#ff0000" name="color"><bR>
							<i class="fa fa-info-circle"></i><small> Pick background of tag </small>
						</center><br>

						<label class="fancy-radio">
							<input name="icon" value="fa fa-gear" type="radio" checked>
							<span><i></i>
								<o class="fa fa-gear"></o>
							</span>
						</label>
						<label class="fancy-radio">
							<input name="icon" value="fa fa-sitemap" type="radio">
							<span><i></i>
								<o class="fa fa-sitemap"></o>
							</span>
						</label>
						<label class="fancy-radio">
							<input name="icon" value="fa fa-comments" type="radio">
							<span><i></i>
								<o class="fa fa-comments"></o>
							</span>
						</label>
						<label class="fancy-radio">
							<input name="icon" value="fa fa-shield" type="radio">
							<span><i></i>
								<o class="fa fa-shield"></o>
							</span>
						</label>
						<label class="fancy-radio">
							<input name="icon" value="fa fa-legal" type="radio">
							<span><i></i>
								<o class="fa fa-legal"></o>
							</span>
						</label>
						<label class="fancy-radio">
							<input name="icon" value="fa fa-bug" type="radio">
							<span><i></i>
								<o class="fa fa-bug"></o>
							</span>
						</label>
						<label class="fancy-radio">
							<input name="icon" value="fa fa-code" type="radio">
							<span><i></i>
								<o class="fa fa-code"></o>
							</span>
						</label>
						<label class="fancy-radio">
							<input name="icon" value="fa fa-star" type="radio">
							<span><i></i>
								<o class="fa fa-star"></o>
							</span>
						</label>

						<p><input type="text" class="form-control" placeholder="TAG Name" name="tag"></p>

						<center><button type="submit" name="insert" class="btn btn-default btn-xs">INSERT TAG</button></center>
					</form>
				</div>
			</div>
		</div>
	</div>

<?php
}
if (Config::isLogged(Config::getUser()) && (Config::isAdmin(Config::getUser()) || $profile->id == Config::getUser())) { ?>
	<div id="moneym" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<p>Changing of Money</p>
					<form method="post" action="#">
						<div class="input-group">
							<span class="input-group-addon">Money</span>
							<input class="form-control" value="<?php echo $profile->Money ?>" type="text" name="money_n" required>
						</div><br>
						<div class="input-group">
							<span class="input-group-addon">Bank</span>
							<input class="form-control" value="<?php echo $profile->Bank ?>" type="text" name="bank_n" required>
						</div>
						<small><i class="fa fa-info-circle"></i> User will receive a notifications after the changes!</small>
						<br><br>
						<button type="submit" name="mon_submit" class="btn btn-warning btn-block"><i class="fa fa-legal"></i> Update</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="premiumed" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<p>Changing of Premium Points</p>
					<form method="post" action="#">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-pencil"></i></span>
							<input class="form-control" placeholder="<?php echo $profile->GoldPoints ?>" type="text" name="pointsp" required>
						</div>
						<small><i class="fa fa-info-circle"></i> User will receive a notifications after the changes!</small>
						<br><br>
						<button type="submit" name="ppr_submit" class="btn btn-warning btn-block"><i class="fa fa-legal"></i> Update</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="small-modals" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<p>Insert carefully the email!</p>
					<form method="post" action="#">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-pencil"></i></span>
							<input class="form-control" placeholder="New email" type="text" name="email" required>
						</div>
						<small><i class="fa fa-info-circle"></i> User will receive a notifications after the changes!</small>
						<br><br>
						<button type="submit" name="email_submit" class="btn btn-primary btn-block"><i class="fa fa-check-circle"></i> CHANGE</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php if (Config::getData("users", "Admin", Config::getUser()) > 0) { ?>
	<?php if (isset($_POST['psuspend'])) {

		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
		} else {
			$reason = htmlspecialchars($_POST['sreason']);
			$days = htmlspecialchars($_POST['sdays']);

			if (!$_POST['sreason'] || !$_POST['sdays']) {
				echo '<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> You left fields blank (reason & days).
			</div>';
			} else {

				if ($days == 999) {
					$suspend = Config::$g_con->prepare('INSERT INTO `kenny_suspend` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Days`,`Reason`,`SuspendDate`,`ExpireDate`,`Permanent`) VALUES (?,?,?,?,?,?,?,?,?)');
					$suspend->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), $days, $reason, date('d/m/Y H:i', $time), 0, 1));

					$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
					$sanctions->execute(array(date('d/m/Y H:i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $reason));
				} else {
					$suspend = Config::$g_con->prepare('INSERT INTO `kenny_suspend` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Days`,`Reason`,`SuspendDate`,`ExpireDate`,`Permanent`) VALUES (?,?,?,?,?,?,?,?,?)');
					$expire = (86400 * $days);
					$suspend->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), $days, $reason, date('Y-m-d H-i', $time), date('Y-m-d H-i', $time + $expire), 0));

					$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
					$sanctions->execute(array(date('Y-m-d H-i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $reason));
				}
	?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button><i class="fa fa-info-circle"></i> The player was successfully suspended.
				</div>
				<meta http-equiv="refresh" content="1" />
	<?php }
		}
	} ?>

	<?php if (isset($_POST['punsuspend'])) {
		$k = Config::$g_con->prepare("DELETE FROM `kenny_suspend` WHERE `UserID` = ?");
		$k->execute(array($profile->id)); ?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button><i class="fa fa-info-circle"></i> The player was successfully unsuspended.
		</div>
		<meta http-equiv="refresh" content="2" />
	<?php } ?>

<?php } ?>


<?php if (Config::getData("users", "Admin", Config::getUser()) > 1) { ?>
	<?php if (isset($_POST['aban'])) {

		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
		} else {
			$areason = htmlspecialchars($_POST['areason']);
			$atime = htmlspecialchars($_POST['atime']);

			if (!$_POST['areason'] || !$_POST['atime']) {
				echo '<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> You left fields blank (reason & time).
			</div>';
			} else {

				if ($atime == 999) {
					$quee = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
					$quee->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), 4, $atime, $areason, date('d/m/Y H:i', $time)));

					$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
					$sanctions->execute(array(date('d/m/Y H:i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $areason));
				} else {
					$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
					$que->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), 0, $atime, $areason, date('d/m/Y H:i', $time)));

					$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
					$sanctions->execute(array(date('d/m/Y H:i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $areason));
				}
	?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button><i class="fa fa-info-circle"></i> The player was successfully sanctioned.
				</div>
				<meta http-equiv="refresh" content="1" />
	<?php }
		}
	} ?>

	<?php if (isset($_POST['awarn'])) {

		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
		} else {

			$areason = htmlspecialchars($_POST['areason']);
			$atime = htmlspecialchars($_POST['atime']);

			if (!$_POST['areason']) {
				echo '<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> You left fields blank (reason).
			</div>';
			} else {
				if ($profile->Warnings == 2) {
					echo '
<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> This player are 2 warnings, please give ban.
			</div>
';
				} else {
					$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
					$que->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), 1, $atime, $areason, date('d/m/Y H:i', $time)));

					$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
					$sanctions->execute(array(date('d/m/Y H:i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $areason));
	?>
					<div class="alert alert-success alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button><i class="fa fa-info-circle"></i> The player was successfully sanctioned.
					</div>
					<meta http-equiv="refresh" content="1" />
	<?php }
			}
		}
	} ?>


	<?php if (isset($_POST['ajail'])) {

		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
		} else {

			$areason = htmlspecialchars($_POST['areason']);
			$atime = htmlspecialchars($_POST['atime']);

			if (!$_POST['areason'] || !$_POST['atime']) {
				echo '<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> You left fields blank (reason & time).
			</div>';
			} else {
				$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
				$que->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), 3, $atime, $areason, date('d/m/Y H:i', $time)));

				$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
				$sanctions->execute(array(date('d/m/Y H:i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $areason));
	?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button><i class="fa fa-info-circle"></i> The player was successfully sanctioned.
				</div>
				<meta http-equiv="refresh" content="1" />
	<?php }
		}
	} ?>

	<?php if (isset($_POST['adm'])) {

		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
		} else {

			$areason = htmlspecialchars($_POST['areason']);
			$atime = htmlspecialchars($_POST['atime']);

			if (!$_POST['areason'] || !$_POST['atime']) {
				echo '<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> You left fields blank (reason & time).
			</div>';
			} else {
				$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
				$que->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), 5, $atime, $areason, date('d/m/Y H:i', $time)));

				$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
				$sanctions->execute(array(date('d/m/Y H:i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $areason));
	?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button><i class="fa fa-info-circle"></i> The player was successfully sanctioned.
				</div>
				<meta http-equiv="refresh" content="1" />
	<?php }
		}
	} ?>

	<?php if (isset($_POST['amute'])) {

		if (Config::isAdmin($profile->id) && Config::getData("users", "Admin", $profile->id) > Config::getData("users", "Admin", Config::getUser())) {
			Config::gotoPage('profile/' . $profile->name . '', 0, 'danger', 'You are not allowed to punish higher admins!');
		} else {

			$areason = htmlspecialchars($_POST['areason']);
			$atime = htmlspecialchars($_POST['atime']);

			if (!$_POST['areason'] || !$_POST['atime']) {

				echo '<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button><i class="fa fa-info-circle"></i> You left fields blank (reason).
			</div>';
			} else {

				$que = Config::$g_con->prepare('INSERT INTO `panel_sanctions` (`UserName`,`UserID`,`AdminName`,`AdminID`,`Type`,`Time`,`Reason`,`Date`) VALUES (?,?,?,?,?,?,?,?)');
				$que->execute(array($profile->name, $profile->id, Config::getData("users", "name", Config::getUser()), Config::getData("users", "id", Config::getUser()), 2, $atime, $areason, date('d/m/Y H:i', $time)));

				$sanctions = Config::$g_con->prepare('INSERT INTO `sanctions` (`Time`,`Player`,`By`,`Userid`,`Type`,`Reason`) VALUES (?,?,?,?,?,?)');
				$sanctions->execute(array(date('d/m/Y H:i', $time), $profile->name, Config::getData("users", "name", Config::getUser()), $profile->id, 0, $areason));
	?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button><i class="fa fa-info-circle"></i> The player was successfully sanctioned.
				</div>
				<meta http-equiv="refresh" content="1" />
	<?php }
		}
	} ?>
<?php } ?>


<?php if (Config::getData("users", "Admin", Config::getUser()) > 1) { ?>
	<div id="sanctioneaza" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<h5><i class="fa fa-cog"> </i> Sanction user</h5>
					<Hr>
					<form method="post">
						<input style="width: 100%;" class="form-control" placeholder="Reason" name="areason" type="text">
						<br>
						<input style="width: 100%;" class="form-control" placeholder="Time (999 for ban permanent)" name="atime" type="number">
						<br>
						<button class="btn btn-danger btn-block" name="aban" type="submit">Ban</button>
						<br>
						<button class="btn btn-danger btn-block" name="awarn" type="submit">Warn</button>
						<br>
						<button class="btn btn-primary btn-block" name="amute" type="submit">Mute</button>
						<br>
						<button class="btn btn-primary btn-block" name="ajail" type="submit">Jail</button>
						<br>
						<button class="btn btn-primary btn-block" name="adm" type="submit">DM</button>
						<br>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php if (Config::getData("users", "Admin", Config::getUser()) > 0) { ?>
	<div id="suspendeaza" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<h5><i class="fa fa-remove"> </i> Suspend player</h5>
					<Hr>
					<form method="post">
						<input style="width: 100%;" class="form-control" placeholder="Reason" name="sreason" type="text">
						<br>
						<input style="width: 100%;" class="form-control" placeholder="Days (999 for permanent)" name="sdays" type="number">
						<br>
						<button class="btn btn-danger btn-block" name="psuspend" type="submit">Suspend</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="unsuspendeaza" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<h5><i class="fa fa-cog"> </i> Unsuspend player</h5>
					<Hr>
					<form method="post">
						<center>Esti sigur ca vrei sa-i dai unsuspend lui <?php echo $profile->name ?>?</center>
						<br>
						<button class="btn btn-success btn-block" name="punsuspend" type="submit">Sunt sigur!</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<div class="page-content">
	<div class="row-fluid">
		<div class="span12">

			<div class="card bg-dark-2 text-white">
				<div class="card-body">
					<div class="card-text">
						<div class="row justify-content-center">
							<div class="col-xl-8">
								<ul class="nav nav-justified nav-tabs text-white" id="justifiedTab" role="tablist">
									<li class="nav-item text-white">
										<a aria-controls="home" aria-selected="true" class="nav-link  text-white active" data-toggle="tab" href="#home" id="home-tab" role="tab">Profile</a>
									</li>
									<li class="nav-item text-white">
										<a aria-controls="profile" aria-selected="false" class="nav-link text-white" data-toggle="tab" href="#profile" id="profile-tab" role="tab">Faction History</a>
									</li>
									<?php if (Config::isLogged() && (Config::isAdmin(Config::getUser()) || $profile->id == Config::getUser()))
										echo '
</li>
<li class="nav-item text-white">
<a aria-controls="profile" aria-selected="false" class="nav-link text-white" data-toggle="tab" href="#admintools" id="profile-tab" role="tab">Admin Tools</a>
</li>
<li class="nav-item text-white">
<a aria-controls="profile" aria-selected="false" class="nav-link text-white" data-toggle="tab" href="#comp" id="profile-tab" role="tab">Complaints</a>
</li>
';
									?>

								</ul>
								<div class="tab-content  text-white" id="justifiedTabContent">
									<div aria-labelledby="home-tab" class="tab-pane fade show active" id="home" role="tabpanel">
										<div class="row profile justify-content-start">
											<div class="col text-center" style="text-align: center; margin-left: auto; margin-right: auto">
												<span class="text-info text-center">
													<b title="User ID: 474205"><?php echo $profile->name; ?></b>
												</span>
												<br><br>
												<br>
												<div class="d-none d-sm-block">
													<img src="<?php echo Config::$_PAGE_URL; ?>assets/img/skins/Skin_<?php echo $profile->CChar; ?>.png" style="height: 400px; text-align: center" alt="Skin" title="Skin 184">
												</div>
												<div class="d-block d-sm-none">
													<img src="<?php echo Config::$_PAGE_URL; ?>assets/img/skins/Skin_<?php echo $profile->CChar; ?>.png" style="height: 200px; text-align: center" alt="me">
												</div>
												<hr>
												<br>
												<a href="<?php echo Config::$_PAGE_URL; ?>complaints/create/<?php echo $profile->name; ?>" class="btn btn-danger btn-lg" type="button">Reclamatie</a>
												<br><br>
												<?php echo ($profile->Status ? '<i class="material-icons text-success">brightness_1</i> Online' : '<i class="material-icons text-danger">brightness_1</i> Offline') ?>
												<br><br><br>
											</div>
											<div class="col-sm-6">
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Faction
															</div>
															<div class="col-7">
																<?php echo Config::factionName($profile->name, $profile->Member) ?>
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Level
															</div>
															<div class="col-7">
																<?php echo Config::getData("users", "Level", $profile->id) ?>
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Playing Hours
															</div>
															<div class="col-7">
																<?php echo Config::getData("users", "ConnectedTime", $profile->id) ?>
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Phone Number
															</div>
															<div class="col-7">
																<?php echo Config::getData("users", "PhoneNR", $profile->id) ?>
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Joined
															</div>
															<div class="col-7">
																<i class="icon-heart blue"></i>
																<?php echo Config::getData("users", "RegisterDate", $profile->id) ?>
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Last Online
															</div>
															<div class="col-7">
																<?php echo Config::getData("users", "LastOn", $profile->id) ?>
															</div>
														</div>
													</div>
												</div>
												<?php if (Config::isLogged() && (Config::isAdmin(Config::getUser()) || $profile->id == Config::getUser())) { ?>
													<div class="bg-dark-2">
														<div class="card-body">
															<div class="row">
																<div class="col-5 text-right text-white-50">
																	Money
																</div>
																<div class="col-7">
																	$<?php echo Config::getData("users", "Money", $profile->id) ?> / $<?php echo Config::getData("users", "Bank", $profile->id) ?>
																</div>
															</div>
														</div>
													</div>
													<div class="bg-dark-2">
														<div class="card-body">
															<div class="row">
																<div class="col-5 text-right text-white-50">
																	Materials
																</div>
																<div class="col-7">
																	<?php echo Config::getData("users", "Materials", $profile->id) ?>
																</div>
															</div>
														</div>
													</div>
													<div class="bg-dark-2">
														<div class="card-body">
															<div class="row">
																<div class="col-5 text-right text-white-50">
																	Bugged Points
																</div>
																<div class="col-7">
																	<?php echo Config::getData("users", "HPoints", $profile->id) ?>
																</div>
															</div>
														</div>
													</div>
													<div class="bg-dark-2">
														<div class="card-body">
															<div class="row">
																<div class="col-5 text-right text-white-50">
																	Premium
																</div>
																<div class="col-7">
																	Yes (<?php echo Config::getData("users", "GoldPoints", $profile->id) ?> points)
																	<a href="<?php echo Config::$_PAGE_URL; ?>shop">
																		<i class="material-icons text-success">add_box</i>
																	</a>
																</div>
															</div>
														</div>
													</div>
													<div class="bg-dark-2">
														<div class="card-body">
															<div class="row">
																<div class="col-5 text-right text-white-50">
																	Email
																</div>
																<div class="col-7">
																	<?php echo Config::getData("users", "Email", $profile->id) ?> <a href="https://bluepanel.bugged.ro/changemail"><i class="material-icons text-info">edit</i></a>
																</div>
															</div>
														</div>
													</div>
												<?php } ?>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Warnings
															</div>
															<div class="col-7">
																<?php echo Config::getData("users", "Warns", $profile->id) ?>/3
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Faction Warns
															</div>
															<div class="col-7">
																<?php echo Config::getData("users", "FWarn", $profile->id) ?>/3
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Faction Punish
															</div>
															<div class="col-7">
																<span><?php echo Config::getData("users", "FPunish", $profile->id) ?>/60</span>
															</div>
														</div>
													</div>
												</div>
												<div class="bg-dark-2">
													<div class="card-body">
														<div class="row">
															<div class="col-5 text-right text-white-50">
																Forum Profile
															</div>
															<div class="col-7">
																<a href="https://forum.bugged.ro/profile/78726-profil">ID 78726 (click)</a>
															</div>
														</div>
													</div>
												</div>
											</div>

											<div class="col-lg-3">
												<div class="">
													<div class="">
														<div class="skill-name">
															Arms Dealer - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Pizza Boy - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Bus Driver - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Farmer - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Boat Transporter - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Fisherman - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Trucker - Bronze 4</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Garbage man - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div>
												</div>
												<div class="">
													<div class="">
														<div class="skill-name">
															Gambling - Diamond</div>
														<div class="progress" style="">
															<div class="progress-bar skill-diamond-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-diamond-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-diamond-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Rob - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Wang Dealership - Bronze 3</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div>
												</div>
												<div class="">
													<div class="">
														<div class="skill-name">
															Paintball - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															War - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Last Car Standing - Bronze 1</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div>
												</div>
												<div class="">
													<div class="">
														<div class="skill-name">
															Summer Quests - Bronze 1 (0 points)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div>
												</div><br>
												<div class="">
													<div class="">
														<div class="skill-name">
															Police Officer - Bronze 1 (0 days)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Paramedic - Bronze 1 (0 days)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Hitman - Bronze 1 (0 days)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															News Reporter - Bronze 1 (0 days)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Taxi Driver - Bronze 1 (0 days)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															Gang Member - Bronze 1 (0 days)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="skill-name">
															School Instructor - Bronze 1 (0 days)</div>
														<div class="progress" style="">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="progress">
															<div class="progress-bar skill-bronze-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div aria-labelledby="profile-tab" class="tab-pane fade" id="admintools" role="tabpanel">
										
										<div class="col-lg-5">
											<br>
											<br>
											<div class="card  bg-purple text-white">
												<div class="card-body">
													<p class="card-title typography-headline">
														Player Punish Log
													</p>
												</div>
											</div>

											<br>
											<br>
											<div class="card  bg-purple text-white">
												<div class="card-body">
													<p class="card-title typography-headline">
														Suspend Form Panel
													</p>
												</div>
											</div>

											<div class="card bg-dark-2 text-white">
												<div class="card-body">
													<div class="card-text">
														<div>
															<form method="post">
																<input style="width: 100%;" class="form-control  text-white" placeholder="Reason" name="areason" type="text">
																<br>
																<input style="width: 100%;" class="form-control  text-white" placeholder="suspend for 1 day" name="atime" type="number">
																<br>
																<button class="btn btn-danger btn-lg active" name="atime" type="submit"> Suspend</button>
															</form>
														</div>
													</div>
												</div>
											</div>
											<br>
											<div class="card  bg-purple text-white">
												<div class="card-body">
													<p class="card-title typography-headline">
														Ban player
													</p>
												</div>
											</div>

											<div class="card bg-dark-2 text-white">
												<div class="card-body">
													<div class="card-text">
														<div>
															<form method="post">
																<input style="width: 100%;" class="form-control  text-white" placeholder="Reason" name="areason" type="text">
																<br>
																<input style="width: 100%;" class="form-control  text-white" placeholder="Ban for 1 day" name="aban" type="number">
																<br>
																<button class="btn btn-danger btn-lg active" name="aban" type="submit"> Ban!</button>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>
									<div aria-labelledby="profile-tab" class="tab-pane fade" id="profile" role="tabpanel">
										<ul class="list-group">
											<?php
											$wcs = Config::$g_con->prepare('SELECT * FROM `faction_logs` WHERE `player` = ? ORDER BY `id` DESC LIMIT 50');
											$wcs->execute(array($profile->id));
											while ($fhistory = $wcs->fetch(PDO::FETCH_OBJ)) {
												echo '
<li class="list-group-item list-group-item-info">
<img class="float-left rounded-circle fh-avatar" alt="Jovetic s avatar" src="https://bluepanel.bugged.ro/img/avatars/40/' . $profile->CChar . '.png">
' . $fhistory->Text . '.<br> <i class="material-icons text-muted">access_time</i> ' . Config::timeAgo($fhistory->time) . '
</li>
';
											}
											?>
										</ul>
									</div>
									<div aria-labelledby="contact-tab" class="tab-pane fade" id="contact" role="tabpanel">
									</div>
									<div aria-labelledby="complaints-tab" class="tab-pane fade" id="complaints" role="tabpanel">
									</div>
								</div>
							</div>
							<div class="col col-xl-4">
								<br>
								<div class="card  bg-purple text-white">
									<div class="card-body">
										<p class="card-title typography-headline">
											Vehicles
										</p>
									</div>
								</div>
								<table class="table table-stripped table-dark table-responsive-sm">
									<thead>
										<tr>
											<th>Image</th>
											<th class="">Name</th>
											<th class="">Stats</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$wcode = Config::$g_con->prepare('SELECT * FROM `cars` WHERE `Owner` = ?');
										$wcode->execute(array($profile->name));
										if (!$wcode->rowCount());
										else {
											while ($car = $wcode->fetch(PDO::FETCH_OBJ)) {
												echo '
<tr class="warning">
<td class="center">
<img src="' . Config::$_PAGE_URL . 'assets/img/vehicles/' . $car->Model . '.jpg" alt="470" title="470" style="height: 65px">
</td>
<td>
' . Config::$_vehicles[$car->Model] . '
<br>
<i class="material-icons" style="color: #99311E">looks_one</i>
<i class="material-icons" style="color: #4C991E">looks_two</i>
<p>
<br><b class="text-secondary">VIP Vehicle</b><br>
<b>' . ($car->Text != "-" ? '<b>' . $car->Text . '</b>' : '') . '</b>
</p>
</td>
<td>
<b>' . $car->KM . ' km</b><br>
Age: <b>' . $car->Days . ' days</b><br>
</td>
</tr>
';
											}
										}
										?>

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
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/jquery/jquery.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/js/map.min.js"></script>
<script>
	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-toggle="popover"]').popover();
	});
</script>