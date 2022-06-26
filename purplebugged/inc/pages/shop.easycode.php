<?php 
if(!Config::isLogged()) Config::gotoPage("",0,"danger","You must be logged in to can access donation page!");

if(isset(Config::$_url[1]) && Config::$_url[1] == "success") {

	if(!isset($_COOKIE['PaymentAccess'])) Config::gotoPage("payment",0,"warning","Your session timed out by waiting for too long! Click again on 'Proceed >>' button to check again your payment.");

	else {

		$w = Config::$g_con->prepare('SELECT `ID`,`Amount`,`Message` FROM `wcode_donations` WHERE `Username` = ? AND `Activated` = 0');

		$w->execute(array(Config::getNameFromID(Config::getUser())));

		if($w->rowCount()) {

			if(isset($_POST['accepted'])) {

				$s = Config::$g_con->prepare('SELECT `Amount` FROM `wcode_donations` WHERE `ID` = ?');

				$s->execute(array($_POST['accepted']));

				$format = $s->fetch(PDO::FETCH_OBJ);

				$points = ($format->Amount)*50;

				$done = Config::$g_con->prepare('UPDATE `users` SET `GoldPoints` = `GoldPoints` + ? WHERE `id` = ?');

				$done->execute(array($points,Config::getUser()));

				$notif = '[Donation #'.$_POST['accepted'].'] '.$points.' Premium Points has entered into your account!';

				$link = Config::$_PAGE_URL.'profile/' . Config::getNameFromID(Config::getUser());

				Config::makeNotification(Config::getUser(),Config::getNameFromID(Config::getUser()),$notif,Config::getUser(),Config::getData("users","name",Config::getUser()),$link);

				$log = ''.Config::getNameFromID(Config::getUser()).' has accepted payment #'.$_POST['accepted'].' and got '.$points.' Premium Points.';

				$done = Config::$g_con->prepare('INSERT INTO `wcode_donations_logs` (`UserID`,`UserName`,`Log`,`IP`) VALUES (?,?,?,?)');

				$done->execute(array(Config::getUser(),Config::getNameFromID(Config::getUser()),$log,$_SERVER['REMOTE_ADDR']));

				$done = Config::$g_con->prepare('UPDATE `wcode_donations` SET `Activated` = 1, `ActivatedOn` = ? WHERE `ID` = ?');

				$done->execute(array(date("Y-m-d H:i:s"),$_POST['accepted']));

				Config::gotoPage('profile/'.Config::getNameFromID(Config::getUser()).'',0,"success","Premium Points has been delivered in your account already! Check it out.");

			}

			while($donation = $w->fetch(PDO::FETCH_OBJ)) {

				echo '

				<div class="col-md-3">

					<div class="panel" style="padding: 10px">

						<h5>

							<center>5/10/20 EURO<br>=<br>250/500/1000 PREMIUM POINTS</center>

						</h5>

					</div>

				</div>

				<div class="col-md-3">

					<div class="panel" style="padding: 13px">

						<h5>

							<center>

								<p>Account Premium Points: '.Config::getData("users","GoldPoints",Config::getUser()).'</p>

								<p>Donation amount: '.$donation->Amount.' <i class="fa fa-euro"></i></p>

							</center>

						</h5>

					</div>

				</div>

				<div class="col-md-3">

					<div class="panel" style="padding: 23px">

						<h5>

							<center>

								<h4><font color="lightgreen">+</font> '. ($donation->Amount)*50 .' PPoints</h4>

							</center>

						</h5>

					</div>

				</div>

				<div class="col-md-3">

					<div class="panel" style="padding: 27px">

						<center><form method="post">

							<button type="submit" name="accepted" value="'.$donation->ID.'" class="btn btn-success btn-sm">Accept</button>

						</form></center>

					</div>

				</div><div class="clearfix"></div>

				';

			}

			echo '<div class="clearfix"></div>';

		} else Config::gotoPage("",0,"danger","You cannot access this page by security reasons! Attempting in accessing abusive this page might ban the account.");

	}

} else if(isset(Config::$_url[1]) && Config::$_url[1] == "proceed") {

	Config::checkDon();

	$w = Config::$g_con->prepare('SELECT `ID` FROM `wcode_donations` WHERE `Username` = ? AND `Activated` = 0');

	$w->execute(array(Config::getNameFromID(Config::getUser())));

	if($w->rowCount()) {

		echo Config::csSN("success","Your payment has been accepted with success! Wait few seconds to be redirected...");

		Config::gotoPage("payment/success",2,"success","Payment has been accepted with success! Please continue the following steps.");

		setcookie("PaymentAccess", "access", time()+60);

	} else {

		echo Config::csSN("danger","We couldn't process your payment request. Make sure you finalised the payment and also you've inserted exactly same username as yours!<br>In case of problems, open a ticket with your problem to can be re-evaluated!", false);

		Config::gotoPage("tickets/create",4,"info","Select category named <u>Donation problems</u> and describe the problem including username, amount and message you inserted there.");

	}

} else if(!isset(Config::$_url[1])) {

?>
<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Change display language / Schimba limba de afisare a site-ului
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<a href="https://bluepanel.bugged.ro/ro"><img class="img-rounded" src="https://bluepanel.bugged.ro/img/ro.png" alt="Romana" width="40" height="40"></a>
<a href="https://bluepanel.bugged.ro/en"><img class="img-rounded" src="https://bluepanel.bugged.ro/img/en.png" alt="Engleza" width="40" height="40"></a>
</div>
</div>
</div>
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Puncte premium
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
Cei ce vor sa ajute comunitatea pot cumpara puncte premium.<br>
Punctele premium pot fi folosite pentru a lua diverse chestii pe server folosind comanda /shop in joc.<br>
(!) Punctele premium nu se pot transfera intre conturi!
<hr>
<span class="btn btn-success text-white">
<a href="<?php echo Config::$_PAGE_URL; ?>buy" class="text-white">
cumpara puncte premium
</a>
</span>
</div>
</div>
</div>
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Ce poti lua din /shop cu punctele premium
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<span class="badge badge-info">60 puncte</span>
<b>Cont premium</b>
<ul>
<li>payday cu 30% mai mare</li>
<li>dobanda dubla de la banca</li>
<li>poti detine maxim 4 masini personale (userii normali pot detine 2)</li>
<li>acces la comanda /pcolor cu care iti poti schimba culoarea nick-ului in chat (<a href="https://i.imgur.com/6MnZp.png">click</a>)</li>
<li>la /rob se pierd doar 8 puncte (userii normali pierd 10)</li>
<li>acces optiunea /togw pentru dezactivare /w (alti playeri nu iti mai pot scrie pe /w)</li>
<li>pe forum: titlul premium (nickname alb + icon)</li>
<li>pe forum: iti poti seta o imagine de background la profil</li>
</ul>
<span class="badge badge-info">10 puncte</span>
<b>Premium Phone</b>
<ul>
<li>Telefonul din joc arata diferit de telefonul playerilor normali.</li>
<li>Numar de telefon din 4 cifre, la alegere. Ex: 2214.</li>
<li>Se poate folosi comanda /reply pentru a raspunde ultimului SMS primit.</li>
<li>Se pot dezactiva doar apelurile si sa primesti in continuare SMS-uri folosind /turn off calls.</li>
<li>Optiunea /block [id] pentru a bloca temporar (pana la relog) sms-urile si apelurile unui player</li>
<li><a href="https://bluepanel.bugged.ro/premium/iphone">Lista numere Premium disponibile</a></li>
</ul>
<span class="badge badge-info">15 puncte</span>
<b>Schimbare nick</b>
<ul>
<li>daca nu-ti place nick-ul pe care il ai, il poti schimba cu doar 15 puncte premium.</li>
</ul>
<span class="badge badge-info">10 puncte</span>
<b>Clear 20 FP</b>
<ul>
<li>sterge 20 faction punish</li>
</ul>
<span class="badge badge-info">20 puncte</span>
<b>Clear /warns</b>
<ul>
<li>sterge /warn-urile primite de la admini</li>
</ul>
<span class="badge badge-info">20 puncte</span>
<b>Hidden pentru masina</b>
<ul>
<li>culorile hidden sunt culori mai speciale pentru masinile personale. poti aplica o culoare hidden pe masina doar cu puncte premium. <a href="http://wiki.sa-mp.com/wroot/images2/thumb/3/30/Ext_vcolours_2013.jpg/608px-Ext_vcolours_2013.jpg">click aici</a> pentru a vedea cum arata culorile hidden.</li>
<li>poti vedea lista de culori hidden si in joc folosind comanda /colors</li>
</ul>
<span class="badge badge-info">20 puncte</span>
<b>Resetare KM si Vechime pentru un vehicul</b>
<ul>
<li>KM parcursi de vehicul sunt setati la 0.</li>
<li>vechimea vehiculului este setata la 0 zile.</li>
</ul>
<span class="badge badge-info">15 puncte</span>
<b>Frecventa walkie-talkie privata</b>
<ul>
<li>poti avea o frecventa de walkie talkie privata formata din 3 cifre. vei putea pune parola frecventei si doar cei ce au parola pot accesa frecventa. poti schimba parola de cate ori vrei.</li>
<li>un user poate detine mai multe frecvente private de walkie-talkie</li>
<li>frecventele private raman pe cont, nu se pot transfera unui alt player</li>
<li>pentru o lista cu frecventele detinute se poate folosi comanda /freq list</li>
</ul>
<span class="badge badge-info">15 - 50 puncte</span>
<b>Unban [ban temporar]</b>
<ul>
<li>Cei ce au ban temporar pot cumpara unban direct din panel pentru 15 - 50 puncte premium (in functie de numarul de zile pana la expirarea banului)</li>
<li>Pretul unbanului este calculat astfel: 10 puncte premium + 5 puncte premium / zi. Ex: un player banat pentru 3 zile va putea cumpara unban pentru 10 + 5 * 3 = 25 puncte premium.</li>
</ul>
<span class="badge badge-info">50 puncte</span>
<b>Unban [ban permanent]</b>
<ul>
<li>playerii banati pentru inselatorii, spargeri de conturi, incercari de a vinde/cumpara conturi vor putea cumpara unban cu 50 puncte premium, dar vor avea banii setati pe 0 (inclusiv bunuri sterse de pe cont)</li>
<li>playerii banati pentru cheat-uri ce pot fi detectate relativ usor (teleport hack, fly hack, speed hack) pot cumpara unban cu 50 puncte premium</li>
<li>playerii banati pentru cheat-uri folosite pentru a trage mai bine sau pentru a castiga war-uri (aimbot, wall hack etc) nu vor fi debanati vreodata</li>
<li>playerii banati pentru bug abuse nu vor fi debanati vreodata</li>
</ul>
<span class="badge badge-info">100 puncte / 6 luni</span>
<b>Clan</b>
<ul>
<li>clanul iti aduce un subforum pe forumul bugged.ro, in categoria clanuri</li>
<li>achizitionarea unui clan iti aduce un chat pentru clan (/c) si posibilitatea de a invita playeri in clan</li>
<li>cei ce au clan pot adauga inca 4 moderatori pe forum direct din panel. moderatorii clanului se pot sterge tot din panel</li>
<li>playerii ce intra intr-un clan pot atasa gratuit tag-ul clanului la nume.</li>
<li><a href="https://bluepanel.bugged.ro/clan/register">click aici</a> pentru inregistrare unui clan.
</li></ul>
<span class="badge badge-info">25 puncte</span>
<b>+25 sloturi pentru clan</b>
<ul>
<li>daca ai deja un clan inregistrat, poti adauga inca 25 sloturi clanului cu 25 puncte premium</li>
<li>numarul maxim de sloturi pentru un clan este 100</li>
</ul>
<hr>
<span class="badge badge-info">10 puncte</span>
<b>iesire din inchisoare admini</b>
<ul>
<li>se aplica doar pentru playerii ce au primit jail de la admini</li>
<li>licenta de arme va fi setata la 10 ore</li>
</ul>
<hr>
<span class="badge badge-info">500 puncte</span>
<b>Legend</b>
<ul>
<p>
</p><blockquote>
Playerii ce au cumparat 500+ puncte premium pe bugged.ro si detin cont Premium pot primi rank-ul Legend.
</blockquote>
<p></p>
<li>acces la chatul /legend</li>
<li>culoare rosie in chat folosind /legendcolor</li>
<li>badge legend pe profil</li>
<li><a href="https://bluepanel.bugged.ro/premium/vip">activare Legend</a></li>
</ul>
<hr>
<span class="badge badge-info">1 punct</span>
<b>5 x fireworks (artificii)</b>
<ul>
<li>artificiile se pot folosi in joc cu comanda /placefireworks</li>
</ul>
</div>
</div>
</div>
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Vehicule premium
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<ul>
<li>vehiculele premium pot fi cumparate din dealership(/buycar) doar cu puncte premium</li>
<li>vehiculele premium pot fi vandute altor player(/sellcarto)</li>
<li>vehiculele premium nu pot fi vandute la dealership</li>
</ul>
<span class="badge badge-info">200 puncte</span>
<b>Patriot</b><br>
<span class="badge badge-info">220 puncte</span>
<b>Sparrow</b><br>
<span class="badge badge-info">250 puncte</span>
<b>Hotring Racer</b><br>
<span class="badge badge-info">250 puncte</span>
<b>Hotring Racer A</b><br>
<span class="badge badge-info">250 puncte</span>
<b>Hotring Racer B</b><br>
<span class="badge badge-info">250 puncte</span>
<b>Vortex</b><br>
<span class="badge badge-info">300 puncte</span>
<b>Maverick</b><br>
<hr>
<span class="badge badge-info">20 puncte</span>
<b>+1 slot pentru masini</b>
<ul>
<li>adauga 1 slot pentru masini personale</li>
<li>poate fi achizitionat din /v</li>
</ul>
<hr>
<span class="badge badge-info">70-100 puncte</span>
<b>Vehicul VIP</b>
<ul>
<li>acces la comanda /vipname ce permite adaugarea unui text pe vehicul</li>
<li>acces la comanda /vipnamecolor ce permite schimbarea culorii textului afisat pe vehicul</li>
<li>acces la comanda /vipwheels ce permite schimbarea rotilor</li>
<li>vehiculele ce pot fi upgradate cu 100pp sunt Infernus, Sultan, Bullet si Patriot</li>
<li>vehiculele ce pot fi upgradate cu 70pp sunt Sandking, Turismo, Cheetah, Elegy si Banshee</li>
</ul>
<p></p>
</div>
</div>
</div>

</div>
</div>
</div>
<?php

}

?>

<script>

$(function()

{

	$('[data-toggle="popover"]').popover();

});

</script>