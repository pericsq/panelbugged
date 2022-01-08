

<?php

ob_start();

error_reporting(1);

if(!file_exists('inc/pages/' . self::$_url[0] . '.p.php') && strlen(self::$_url[0])) Config::gotoPage("");

$_SESSION['render'] = microtime(true);



$co = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Status` = 0'); $co->execute();

if(!Config::isLogged()) { $tickets = 0; $unban = 0; $complaints = 0; }

else {

	if(Config::isAdmin(Config::getUser())) {

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_tickets` WHERE `Status` = 0'); $get->execute();

		$tickets = $get->rowCount();

		

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_unban` WHERE `Status` = 0'); $get->execute();

		$unban = $get->rowCount();

		

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Status` = 0'); $get->execute();

		$complaints = $get->rowCount();

	} else {

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_tickets` WHERE `Status` = 0 AND `UserID` = ?'); $get->execute(array(Config::getUser()));

		$tickets = $get->rowCount();

		

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_unban` WHERE `Status` = 0 AND `UserID` = ?'); $get->execute(array(Config::getUser()));

		$unban = $get->rowCount();

		

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Status` = 0 AND `UserID` = ? OR `Status` = 0 AND `AccusedID` = ?'); $get->execute(array(Config::getUser(),Config::getUser()));

		$complaints = $get->rowCount();

	}

}

?>


<!doctype html>

<html lang="en">

	

<head>


		<meta charset="utf-8">

		<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

		<!-- VENDOR CSS -->

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/font-awesome/css/font-awesome.min.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/themify-icons/css/themify-icons.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/pace/themes/orange/pace-theme-4g.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/css/vendor/animate/animate.min.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/x-editable/bootstrap3-editable/css/bootstrap-editable.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap-tour/css/bootstrap-tour.min.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/jqvmap/jqvmap.min.css">

		

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/toastr/toastr.min.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/sweetalert2/sweetalert2.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap-markdown/bootstrap-markdown.min.css">

		<!-- MAIN CSS -->

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/css/main.min.css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/css/skins/sidebar-nav-darkgray.css" type="text/css">

		<link rel="stylesheet" href="<?php echo Config::$_PAGE_URL; ?>assets/css/skins/navbar3.css" type="text/css">

		<!-- ICONS -->

		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo Config::$_PAGE_URL; ?>assets/img/apple-icon.png">

		<link rel="icon" type="image/png" sizes="96x96" href="<?php echo Config::$_PAGE_URL; ?>assets/img/favicon.png">

		<script src="<?php echo Config::$_PAGE_URL ?>assets/vendor/jquery/jquery.min.js"></script>

		<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>

		<script src="<?php echo Config::$_PAGE_URL ?>assets/js/bootbox.min.js"></script>

	</head>


<!-- -->

<!-- -->
	<body>


	<?php

	if(isset($_GET['n_seen']) && isset($_GET['n_id']) && Config::isLogged()) {

		if($_GET['n_seen'] == "on" && is_numeric($_GET['n_id'])) {

			$check = Config::$g_con->prepare('SELECT `ID` FROM `wcode_notifications` WHERE `ID` = ?');

			$check->execute(array($_GET['n_id']));

			if($check->rowCount()) {

				$nread = Config::$g_con->prepare('UPDATE `wcode_notifications` SET `Seen` = 1 WHERE `ID` = ?');

				$nread->execute(array($_GET['n_id']));

			}

		}

	}

	if(!Config::isLogged()) 

	{

		if(isset($_POST['recover_submit'])) {



			$wc = Config::$g_con->prepare('SELECT `id`,`name`,`Email` FROM `users` WHERE `Email` = ?');

			$wc->execute(array($_POST['email']));

			if($wc->rowCount()) {

				$email = $wc->fetch(PDO::FETCH_OBJ);

				

				$wcodero = Config::$g_con->prepare('DELETE FROM `wcode_recover` WHERE `email` = ?');

				$wcodero->execute(array($email->Email));



				$getcode = Config::generateRandomString(20);

				$em = Config::$g_con->prepare('INSERT INTO `wcode_recover` (`user`,`username`,`email`,`token`,`time`) VALUES (?,?,?,?,?)');

				$em->execute(array($email->id,$email->name,$email->Email,$getcode,time()+3600));

				

				$to = $email->Email;

				$from = "4gamingrpg.tk";

				$subject = "Recover your password";

				$message = "This is an automatically email so please do not reply! To reset your sa:mp account from our server you must click on following link: ".Config::$_PAGE_URL."validate/recover/".$getcode."";



				$headers = "From: $from"; 

				$ok = @mail($to, $subject, $message, $headers, "-f " . $from); 



				Config::createSN("success","An email that is active <b>one hour</b> has been sended to account's email if exists! Please check Index and Spam category for the email!");

			} else Config::createSN("danger","Please provide an correct email address!");

		}

	}

	if(isset($_POST['login_submit']) && !Config::isLogged()) {

		if(strlen($_POST['username']) && strlen($_POST['password']))

		{

			$wcodero = Config::$g_con->prepare('SELECT `id`,`IP` FROM `users` WHERE `name` = ? AND `password` = ?');

			$wcodero->execute(array($_POST['username'],md5($_POST['password'])));

			if($wcodero->rowCount()) {

				$account = $wcodero->fetch(PDO::FETCH_OBJ);

				$c = Config::$g_con->prepare("SELECT `Text` FROM `wcode_editables` WHERE `Form` = 'login' AND `Text` = 'secured'"); $c->execute();

				if($c->rowCount()) {

					if($account->IP == $_SERVER['REMOTE_ADDR']) {

						Config::createSN("success","You've been logged in with success!");

						$_SESSION['account_panel'] = $account->id;

					} else Config::createSN("info","Your IP must be the same as the one you had last time in game!"); //nu apare mesajul

				} else {

					Config::createSN("success","You've been logged in with success!");

					$_SESSION['account_panel'] = $account->id;

				}

			}

			else Config::createSN("danger","Please provide valid username and password!");

		}

	}

	?>

		<!-- LOGIN SECURE -->

		<div id="small-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

			<div class="modal-dialog modal-sm" role="document">

				<div class="modal-content">

					<div class="modal-body">

						<p>Login into your account</p>

						<form method="post" action="#">

						<div class="input-group">

							<span class="input-group-addon"><i class="fa fa-user"></i></span>

							<input class="form-control" placeholder="Username" type="text" name="username" required>

						</div><br>

						<div class="input-group">

							<span class="input-group-addon"><i class="fa fa-lock"></i></span>

							<input class="form-control" placeholder="Password" type="password" name="password" required>

						</div>

						<center><button type="button" class="btn btn-link active" data-toggle="modal" data-target="#recoverpass" style="color: #000;"><small>Forgot your password</small></button></center>

						

						<button type="submit" name="login_submit" class="btn btn-primary btn-block"><i class="fa fa-check-circle"></i> LOGIN</button>

						</form>

					</div>

				</div>

			</div>

		</div>

		<div id="recoverpass" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

			<div class="modal-dialog modal-large" role="document">

				<div class="modal-content">

					<div class="modal-body"><br><br><br>

						<p>Recover your account</p>

						<form method="post" action="">

						<div class="input-group">

							<span class="input-group-addon"><i class="fa fa-user"></i></span>

							<input class="form-control" placeholder="Email of your account" type="text" name="email" required>

						</div><br>

						<button type="submit" name="recover_submit" class="btn btn-primary btn-block"><i class="fa fa-envelope-open-o"></i> RECOVER</button>

						</form><br><br><br>

					</div>

				</div>

			</div>

		</div>

		<!-- WRAPPER -->

		<div id="toastr-demo">

		<div id="wrapper">

			<!-- NAVBAR -->

			<nav class="navbar navbar-default navbar-fixed-top">

				<div class="brand">
				
					<a href="<?php echo Config::$_PAGE_URL ?>">
						<center><img style="width: 60%;" src="https://i.imgur.com/rlr21PX.png" style="height: 23px; margin-right: 39px;" alt="CHARS.RO"></center>
					</a>
               
				</div>

				<div class="container-fluid">

					<div id="tour-fullwidth" class="navbar-btn">

						<button type="button" class="btn-toggle-fullwidth"><i class="ti-arrow-circle-left"></i></button>

					</div>

					<form class="navbar-form navbar-left search-form" method="post" action="search">

						<input type="text" class="form-control" placeholder="Search players..." name="search_num">

						<button type="submit" class="btn btn-default" name="search_sub"><i class="fa fa-search"></i></button>

					</form>

					<div id="navbar-menu">

						<ul class="nav navbar-nav navbar-right">

							<?php 

							if(!Config::isLogged())

								echo '<li><a href="" data-toggle="modal" data-target="#small-modal">Login</a></li>';

							else {

								$notif_unread = Config::$g_con->prepare('SELECT * FROM `wcode_notifications` WHERE `UserID` = ? AND `Seen` = 0');

								$notif_unread->execute(array(Config::getUser()));

								

								echo '<li class="dropdown">

									<a href="#" class="dropdown-toggle icon-menu" data-toggle="dropdown" aria-expanded="false">

										'.(!$notif_unread->rowCount() ? '<i class="ti-bell"></i>' : '<i class="ti-bell"></i><span class="badge bg-danger">'.$notif_unread->rowCount().'</span>').'

									</a>

									<ul class="dropdown-menu notifications">

										<li>You have '.$notif_unread->rowCount().' new notifications</li>

										<li>';

										$notif = Config::$g_con->prepare('SELECT * FROM `wcode_notifications` WHERE `UserID` = ? AND `Seen` = 0 ORDER BY `ID` DESC LIMIT 5');

										$notif->execute(array(Config::getUser()));

										$count = 0;

										while($no = $notif->fetch(PDO::FETCH_OBJ)) {

											echo '<a href="'.$no->Link.'?n_seen=on&n_id='.$no->ID.'" class="notification-item" style="background-color: #003104">

												<i class="fa fa-location-arrow custom-bg-orange"></i>

												<p>

													<span class="text">'.$no->Notification.'</span>

													<span class="timestamp">'.Config::timeAgo($no->Date, false).'</span>

												</p>

											</a>';

											$count++;

										}

										$limit = 5 - $count;

										$notif = Config::$g_con->prepare('SELECT * FROM `wcode_notifications` WHERE `UserID` = ? AND `Seen` = 1 ORDER BY `ID` DESC LIMIT '.$limit.'');

										$notif->execute(array(Config::getUser()));

										$count = 0;

										while($no = $notif->fetch(PDO::FETCH_OBJ)) {

											echo '<a href="'.$no->Link.'" class="notification-item">

												<i class="fa fa fa-check custom-bg-green"></i>

												<p>

													<span class="text">'.$no->Notification.'</span>

													<span class="timestamp">'.Config::timeAgo($no->Date, false).'</span>

												</p>

											</a>';

											$count++;

										}

										echo '</li>

									</ul>

								</li>';

								

								echo '<li class="dropdown">

									<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">

										<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.Config::getData("users","CChar",Config::getUser()).'.png" alt="Avatar">

										<span>'.Config::getNameFromID(Config::getUser()).'</span>

									</a>

									<ul class="dropdown-menu logged-user-menu">

										<li><a href="'.Config::$_PAGE_URL.'profile/'.Config::getNameFromID(Config::getUser()).'"><i class="ti-user"></i> <span>My Profile</span></a></li>

										<li><a href="'.Config::$_PAGE_URL.'logout"><i class="ti-power-off"></i> <span>Logout</span></a></li>

									</ul>

								</li>';

							}

							?>

							

						</ul>

					</div>

				</div>

			</nav>

			<!-- END NAVBAR -->

			<!-- LEFT SIDEBAR -->

			<div id="sidebar-nav" class="sidebar">

				<nav>

					<ul class="nav" id="sidebar-nav-menu">

					<?php 

					if(Config::isAdmin(Config::getUser()))

						echo '<li><a href="'.Config::$_PAGE_URL.'adminpanel" '.Config::isActive("adminpanel").'><i class="ti-world"></i> <span class="title">Admin board</span></a></li>';

					if(Config::isLogged() && Config::getData("users","Member",Config::getUser()) != 0 && Config::getData("users","Rank",Config::getUser()) >= Config::$_LEADER_RANK) { ?>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>leaderpanel" <?php echo Config::isActive("leaderpanel"); ?>><i class="ti-hummer"></i> <span class="title">Leader board</span></a></li>

					<?php } ?>	

						<li class="menu-group">Main Navigation</li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>" <?php echo Config::isActive(""); ?>><i class="ti-home"></i> <span class="title">Dashboard</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>search" <?php echo Config::isActive("search"); ?>><i class="ti-search"></i> <span class="title">Cauta un jucator</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>online" <?php echo Config::isActive("online"); ?>><i class="ti-headphone-alt"></i> <span class="title">Jucatori online</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>payment" <?php echo Config::isActive("payment"); ?>><i class="fa fa-paper-plane-o"></i> <span class="title">Shop</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>helper" <?php echo Config::isActive("helper"); ?>><i class="ti-shield"></i> <span class="title">Apply helper</span></a></li>
						
						<li><a href="<?php echo Config::$_PAGE_URL; ?>staff" <?php echo Config::isActive("staff"); ?>><i class="ti-shield"></i> <span class="title">Staff</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>clans" <?php echo Config::isActive("clans"); ?>><i class="ti-comments"></i> <span class="title">Clan-uri</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>banned" <?php echo Config::isActive("banned"); ?>><i class="ti-wheelchair"></i> <span class="title">Jucatori banati</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>factions" <?php echo Config::isActive("factions"); ?>><i class="ti-layout-tab"></i> <span class="title">Factiuni</span></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>complaints" <?php echo Config::isActive("complaints"); ?>><i class="ti-signal"></i> <span class="title">Reclamatii</span> <?php echo ($complaints ? '<span class="badge">'.$complaints.'</span>' : ''); ?></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>tickets" <?php echo Config::isActive("tickets"); ?>><i class="ti-support"></i> <span class="title">Tickete</span> <?php echo ($tickets ? '<span class="badge">'.$tickets.'</span>' : ''); ?></a></li>

						<li><a href="<?php echo Config::$_PAGE_URL; ?>unban" <?php echo Config::isActive("unban"); ?>><i class="ti-face-sad"></i> <span class="title">Cereri debanare</span> <?php echo ($unban ? '<span class="badge">'.$unban.'</span>' : ''); ?></a></li>

						<li class="panel">

							

							<div id="map" class="collapse" aria-expanded="false" style="height: 0px;">

								<ul class="submenu">

									<li><a href="<?php echo Config::$_PAGE_URL; ?>navigate/house">Case</a></li>

									<li><a href="<?php echo Config::$_PAGE_URL; ?>navigate/bizz">Afaceri</a></li>

									<li><a href="<?php echo Config::$_PAGE_URL; ?>navigate/war">Turf-uri</a></li>

								</ul>

							</div>

						</li>

						<li class="panel">

							<a href="#tables" data-toggle="collapse" data-parent="#sidebar-nav-menu" class="collapsed" aria-expanded="false"><i class="ti-key"></i> <span class="title">Statistici</span> <i class="icon-submenu ti-angle-left"></i></a>

							<div id="tables" class="collapse" aria-expanded="false" style="height: 0px;">

								<ul class="submenu">

									<li><a href="<?php echo Config::$_PAGE_URL; ?>top">Top jucatori</a></li>

									<li><a href="<?php echo Config::$_PAGE_URL; ?>dealership">Dealership</a></li>

									<li><a href="<?php echo Config::$_PAGE_URL; ?>properties/houses">Case</a></li>

									<li><a href="<?php echo Config::$_PAGE_URL; ?>/properties/businesses">Afaceri</a></li>

								</ul>

							</div>

						</li>

						<li><a href="#" target="_blank"><i class="fa fa-paper-plane-o"></i> <span class="title">Forum</span></a></li>

					</ul>

				</nav>

			</div>

			<!-- END LEFT SIDEBAR -->

			<!-- MAIN -->

			<div class="main">

				<!-- MAIN CONTENT -->

				<div class="main-content">

					<div class="content-heading clearfix">

						<div class="heading-left">

							<p class="page-subtitle" style="margin-top: 12px"><?php 

							if(!isset(Config::$_url[1]))

								echo ucfirst(Config::$_url[0]); 

							else

								echo ucfirst(Config::$_url[0]) . ' <i>('.Config::$_url[1].')</i>'; 

							?></p>

						</div>

						<small><ul class="breadcrumb">

							<li><i class="fa fa-home"></i> Home</li>

							<li><?php 

							if(!strlen(Config::$_url[0])) echo 'Dashboard';

							else echo ucfirst(Config::$_url[0]); 

							?></li>

						</ul></small>

					</div>

					<div class="container-fluid">
<?php
echo Config::showSN();
?>
