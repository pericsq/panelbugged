<div class="panel" style="padding: 10px">
	<?php
	if (!Config::isLogged()) Config::gotoPage("", 0, "danger", "You must be logged in to can access the page!");
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
	if (!isset(Config::$_url[1])) {
	?>
		<div class="page-content">
			<div class="row-fluid">
				<div class="span12">

					<div class="pull-left control-group">
						<a href="<?php Config::$_PAGE_URL ?>tickets/create" type="button" title="Create" class="btn btn-danger">New Ticket</a>
					</div>
					<br>
					<table id="sample-table-1" class="table table-striped table-bordered table-hover table-dark">
						<thead>
							<tr>
								<th>Title</th>
								<th>
									<i class="icon-time bigger-110 hidden-480"></i>
									Date
								</th>
								<th class="hidden-480">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (Config::getData("users", "Admin", Config::getUser())) {
								$wcs = Config::$g_con->prepare('SELECT `ID`,`Category`,`Status`,`Date`,`UserName` FROM `wcode_tickets` ORDER BY `Date` DESC');
								$wcs->execute(array());
							} else {
								$wcs = Config::$g_con->prepare('SELECT `ID`,`Category`,`Status`,`Date` FROM `wcode_tickets` WHERE `UserID` = ? ORDER BY `Date` DESC ' . Config::limit() . '');
								$wcs->execute(array(Config::getUser()));
							}
							while ($ticket = $wcs->fetch(PDO::FETCH_OBJ)) {
								echo '
				<tr>
					<td><a href="' . Config::$_PAGE_URL . 'tickets/view/' . $ticket->ID . '">' . $ticket->Category . '</a></td>
					<td>' . $ticket->Date . '</td>
					<td>' . (!$ticket->Status ? '<span class="label label-success label-transparent">OPENED</span>' : '<span class="label label-danger label-transparent">CLOSED</span>') . '</td>
				</tr>
			';
							}
							?>
							</tr>
						</tbody>
					</table>
					<div class="card bg-dark-2 text-white">
						<div class="card-body">
							<div class="card-text">
								Total: 1 tickets.
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
		echo Config::create(Config::rows("wcode_tickets", "ID"));
	} else if (Config::$_url[1] == "view" && isset(Config::$_url[2]) && is_numeric(Config::$_url[2])) {
		$wcs = Config::$g_con->prepare('SELECT `Text`,`UserID`,`ID`,`UserName`,`Date`,`Category`,`Status` FROM `wcode_tickets` WHERE `ID` = ?');
		$wcs->execute(array(Config::$_url[2]));
		if (!$wcs->rowCount()) Config::gotoPage("tickets", 0, "danger", "We couldn't find any ticket with this ID!");
		$ticket = $wcs->fetch(PDO::FETCH_OBJ);
		if ($ticket->UserID != Config::getUser() && !Config::getData("users", "Admin", Config::getUser())) Config::gotoPage("tickets", 0, "danger", "You have no rights to access the page!");

	?>
		<div class="page-content">
			<div class="row-fluid">
				<div class="span12">
					<div class="row">
						<div class="col-lg-6">
							<div class="card  bg-purple text-white">
								<div class="card-body">
									<p class="card-title typography-headline">
										Ticket status
									</p>
								</div>
							</div>
							<div class="card bg-dark-2 text-white">
								<div class="card-body">
									<div class="card-text">
										<?php echo Config::xss(Config::clean($ticket->Text)) ?><br>
										<hr>
										<?php
										echo 'Ticket type: <b>' . $ticket->Category . '</b><br>
		';
										?>
										Ticket status: <b><?php echo (!$ticket->Status ? '<span class="label label-success label-transparent">Open</span>' : '<span class="label label-danger label-transparent">Closed</span>') ?></b><br>
										Created at: <b><?php echo $ticket->Date . ' (' . Config::timeAgo($ticket->Date) . ')' ?></b><br>
									</div>
								</div>
							</div>
							<br>
							<div class="card  bg-purple text-white">
								<div class="card-body">
									<p class="card-title typography-headline">
										Created By
									</p>
								</div>
							</div>
							<div class="card bg-dark-2 text-white">
								<div class="card-body">
									<div class="card-text">
										<b>Nickname:</b> <?php echo Config::formatName($ticket->UserName) ?>
										<br>
										<b>Level:</b>test<br>
										<b>Faction:</b> <?php echo Config::factionName($ticket->name, $ticket->Member) ?>
										<br>
										<b>Hours played:</b>test<br>
										<b>Email:</b>test<br>
										<b>Premium points:</b>test<br>
										<hr>
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class="col-lg-6">
							<div class="card  bg-purple text-white">
								<div class="card-body">
									<p class="card-title typography-headline">
										Comments
									</p>
								</div>
							</div>
							<div class="card bg-dark-2 text-white">
								<div class="card-body">
									<?php
									$wcjd = Config::$g_con->prepare('SELECT * FROM `wcode_commentaries` WHERE `Section` = "tickets" AND `TopicID` = ? ORDER BY `ID` ASC');
									$wcjd->execute(array(Config::$_url[2]));
									if (!$wcjd->rowCount()) echo '';

									else {
										while ($comment = $wcjd->fetch(PDO::FETCH_OBJ)) {
											echo '
			<div class="card-text">
			<div class="card bg-dark-2 text-white"><div class="card-text">
			<div class="body  ">
			<span class="green">
			today, 16:24
			</span>
			</div>
			</div>
			<br>
			<div class="text" style="padding-left: 50px">
			<p>
			<span class="name"><i><b>' . Config::formatName($comment->UserName) . '</b></i>';
											if ($comment->UserID == $c->UserID) echo ' <span class="badge badge-info">complaint creator</span>';
											else if ($comment->UserID == $wcjd->AccusedID) echo ' <span class="badge badge-warning">reported player</span>';
											else if (Config::isAdmin($comment->UserID)) echo ' <i class="material-icons text-warning" title="Legend">star</i>';
											echo '<br>
			<span class="comment">		
			' . $comment->Text . '<br>
			</span>
			<span class="float-right">
			</span>
			</p>
			</div>
			</div></div></span>';
										}
									}
									if ($ticket->Status == 0) {
										if (isset($_POST['delete_com']) && Config::getData("users", "Admin", Config::getUser())) {
											$wcjd = Config::$g_con->prepare('DELETE FROM `wcode_commentaries` WHERE `ID` = ?');
											$wcjd->execute(array($_POST['delete_com']));
											Config::gotoPage('tickets/view/' . Config::$_url[2] . '', 0, "success", "You deleted with success the comment!");
										}
										if (isset($_POST['comment_send']) && strlen($_POST['text_comm'])) {
											$wcjd = Config::$g_con->prepare('INSERT INTO `wcode_commentaries` (`UserID`,`UserName`,`Skin`,`Text`,`Section`,`TopicID`) VALUES (?,?,?,?,"complaints", ?)');
											$wcjd->execute(array(Config::getUser(), Config::getData("users", "Name", Config::getUser()), Config::getData("users", "CChar", Config::getUser()), $_POST['text_comm'], Config::$_url[2]));
											Config::gotoPage('tickets/view/' . Config::$_url[2] . '', 0, "success", "Your comment has been published!");
											if ($ticket->UserID != Config::getUser()) {
												$notif = 'New comment has been posted in your ticket!';
												$link = Config::$_PAGE_URL . 'tickets/view/' . Config::$_url[2];
												Config::makeNotification($ticket->UserID, $ticket->UserName, $notif, Config::getUser(), Config::getData("users", "name", Config::getUser()), $link);
											}
										}
									?>
										<form method="post">
											<div class="form-group">
												<label>Welcome a Reply</label>
												<textarea class="input-block-level form-control" placeholder="Write your comment" name="text_comm"></textarea>
												<br><br>
												<input type="submit" name="comment_send" class="btn btn-small btn-danger" value="Post">
											</div>
										</form>
									<?php
									} else { ?>
										<div class="form-group">
											<label>Leave a Reply</label>
											<textarea class="input-block-level form-control" placeholder="You can't reply to this topic, reason: This topic is closed." type="text" disabled=""></textarea>
											<br><br>
											<input type="submit" name="submit" class="btn btn-small btn-danger" value="Post" disabled="">
										</div>
									<?php
									}
									?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
</div>
<?php
	} else if (Config::$_url[1] == "create") {

		if (isset($_POST['submit']) && strlen($_POST['ticket'])) {
			$wcjd = Config::$g_con->prepare('INSERT INTO `wcode_tickets` (`UserID`,`UserName`,`Category`,`Text`) VALUES (?,?,?,?)');
			$wcjd->execute(array(Config::getUser(), Config::getData("users", "name", Config::getUser()), $_POST['category'], $_POST['ticket']));
			Config::gotoPage("tickets", 0, "success", "Your ticked has been posted, soon an admin will take a look of your case!");
		}
?>
	<form method="post">
		<select name="category" style="padding: 7px;background-color: #4c626d;border-radius: 3px;border: 1px solid #5e7986;box-shadow: 0 0 black;">
			<option value="Account problems">Account problems</option>
			<option value="Donation problems">Donation problems</option>
			<option value="Bugs">Bugs</option>
			<option value="Others">Others</option>
		</select><br><br>
		<div class="form-group">
			<label for="contact-message" class="control-label sr-only">Message</label>
			<input placeholder="Enter your text" class="form-control" type="text" name="ticket" style="width: 100%">
			<br>
			<small><i class="fa fa-info-circle"></i> Try to write in detail the problem you have to be more easy for us to solve it faster!</small>
		</div>
		<center><button type="submit" name="submit" class="btn btn-info">Create</button></center>
	</form>
<?php
	}
?>
</div>