<?php

ob_start();

error_reporting(1);

if (!file_exists('inc/pages/' . self::$_url[0] . '.php') && strlen(self::$_url[0])) Config::gotoPage("");

$_SESSION['render'] = microtime(true);



$co = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Status` = 0');
$co->execute();

if (!Config::isLogged()) {
	$tickets = 0;
	$unban = 0;
	$complaints = 0;
} else {

	if (Config::isAdmin(Config::getUser())) {

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_tickets` WHERE `Status` = 0');
		$get->execute();

		$tickets = $get->rowCount();



		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_unban` WHERE `Status` = 0');
		$get->execute();

		$unban = $get->rowCount();



		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Status` = 0');
		$get->execute();

		$complaints = $get->rowCount();
	} else {

		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_tickets` WHERE `Status` = 0 AND `UserID` = ?');
		$get->execute(array(Config::getUser()));

		$tickets = $get->rowCount();



		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_unban` WHERE `Status` = 0 AND `UserID` = ?');
		$get->execute(array(Config::getUser()));

		$unban = $get->rowCount();



		$get = Config::$g_con->prepare('SELECT `ID` FROM `wcode_complaints` WHERE `Status` = 0 AND `UserID` = ? OR `Status` = 0 AND `AccusedID` = ?');
		$get->execute(array(Config::getUser(), Config::getUser()));

		$complaints = $get->rowCount();
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
	<meta charset="utf-8">
	<meta content="initial-scale=1, shrink-to-fit=no, width=device-width" name="viewport">
	<title>bluepanel.bugged.ro | user panel</title>
	<script src="<?php echo Config::$_PAGE_URL; ?>pericolrpg/cdn-cgi/apps/head/29VAPGSEMU1KgjM7TIO47CeGv9w.js"></script>
	<link href="<?php echo Config::$_PAGE_URL; ?>pericolrpg/css/material.min.css" rel="stylesheet">
	<link href="<?php echo Config::$_PAGE_URL; ?>pericolrpg/css/material-custom.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
	<title>Bugged</title>
</head>

<body>
	<div class="main-content">
		<nav class="navbar navbar-expand-xl navbar-dark bg-bluepanel" id="ihatesidebars">
			<a class="navbar-brand" href="<?php echo Config::$_PAGE_URL; ?>pericolrpg/index.html">BUGGED</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<?php
						if (Config::isLogged() && Config::getData("users", "Admin", Config::getUser()) != 0) { ?>

							<a class="nav-link" href="<?php echo Config::$_PAGE_URL; ?>adminpanel">Admin</a>

						<?php } ?>
					</li>
					<li class="nav-item">
						<?php
						if (Config::isLogged() && Config::getData("users", "Member", Config::getUser()) != 0) { ?>

							<a class="nav-link" href="<?php echo Config::$_PAGE_URL; ?>myraport">My Faction</a>

						<?php } ?>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo Config::$_PAGE_URL; ?>group">Factions</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo Config::$_PAGE_URL; ?>clans">Clans</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo Config::$_PAGE_URL; ?>staff">Staff</a>

					</li>
					<li class="nav-item nav-premium">
						<a class="nav-link" href="<?php echo Config::$_PAGE_URL; ?>shop">Premium <span class="nav-item nav-premium">+50%</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo Config::$_PAGE_URL; ?>market">Market</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="<?php echo Config::$_PAGE_URL; ?>#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Topics
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>complaints">Complaints</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>tickets">Tickets</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>unban">Unbans</a>
						</div>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="<?php echo Config::$_PAGE_URL; ?>#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							More
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>questmap">Quest Map</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>search">Search</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>banned">Bans</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>bid">Auctions</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>wars">War-uri</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>top">Leaderboards</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>houses">Case</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>businesses">Afaceri</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>vehicles">Vehicule Personale</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>clothes">Clothes</a>
							<a class="dropdown-item" href="<?php echo Config::$_PAGE_URL; ?>timeonline">Timp online</a>
						</div>
					</li>
				</ul>
			</div>

			<div style="display: contents;">
				<span class="d-xl-none d-lg-none">
				</span>
				<a href="<?php echo Config::$_PAGE_URL; ?>pericolrpg/login" class="d-xl-none d-lg-none">
					<i class="material-icons bg-bluepanel" style="font-size: 40px; padding-right: 0px">notifications</i>
				</a>
				<div class="dropdown d-none d-lg-block">
					<a href="<?php echo Config::$_PAGE_URL; ?>pericolrpg/" data-toggle="dropdown" id="loadnotifications">
						<i class="material-icons bg-bluepanel" style="font-size: 40px">notifications</i>
					</a>
					<div class="notifications dropdown-menu dropdown-menu-right " id="notifications" style="width: 50vw; background: black" class="bg-dark-2">
						<a class="dropdown-item text-white" href="<?php echo Config::$_PAGE_URL; ?>pericolrpg/login.html">Loading...</a>
					</div>
				</div>
				<?php if (!Config::isLogged()) {
					echo '	
							
<ul class="navbar-nav mr-auto">
<li class="nav-item">
<a class="nav-link" href="' . Config::$_PAGE_URL . 'login">Login</a>
</li>
</ul>
';
				} else {
					echo '
								
<div class="dropdown">
<a href="" data-toggle="dropdown">
<img src="https://bluepanel.bugged.ro/img/avatars/100/' . Config::getData("users", "CChar", Config::getUser()) . '.png" height="40" class="rounded-circle" alt="avatar">
<div class="nav-myaccount float-right bg-bluepanel">
' . Config::getNameFromID(Config::getUser()) . ' <br>
Level ' . Config::getData("users", "Level", Config::getUser()) . '
</div>
</a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="' . Config::$_PAGE_URL . 'profile/' . Config::getNameFromID(Config::getUser()) . '">Profilul meu</a>
<a class="dropdown-item" href="' . Config::$_PAGE_URL . '">Informatii cont</a>
<a class="dropdown-item" href="' . Config::$_PAGE_URL . '">Schimba email</a>
<a class="dropdown-item" href="' . Config::$_PAGE_URL . '">Setari forum</a>
<a class="dropdown-item" href="' . Config::$_PAGE_URL . 'logout">Deconectare</a>
</div>
</div>
								
';
				} ?>
		</nav>
	</div>


	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<script src="https://github.com/PericolRPG/antiinspect/blob/main/antiprosti.js"></script>

	<script src="<?php echo Config::$_PAGE_URL; ?>pericolrpgjs/material.min.js"></script>


	<script type="text/javascript" src="<?php echo Config::$_PAGE_URL; ?>pericolrpg/cdn.jsdelivr.net/npm/cookie-bar/cookiebar-latest.minfc12.js?forceLang=ro&amp;tracking=1&amp;thirdparty=1&amp;always=1&amp;noGeoIp=1&amp;showNoConsent=1&amp;privacyPage=http%3A%2F%2Fbluepanel.bugged.ro%2Fprivacy"></script>

	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i|Roboto+Mono:300,400,700|Roboto+Slab:300,400,700&amp;display=swap" rel="stylesheet">
	<script defer src="<?php echo Config::$_PAGE_URL; ?>pericolrpg/static.cloudflareinsights.com/beacon.min.js" data-cf-beacon='{"rayId":"6a89c4396e981fa2","version":"2021.10.0","r":1,"token":"fe410fec29da4a93b9061f12f264a111","si":100}'></script>
</body>

</html>


<?php

echo Config::showSN();


?>