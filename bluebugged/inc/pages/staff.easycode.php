<?php

$w = Config::$g_con->prepare("SELECT `id`,`CChar`,`name`,`Admin`,`Member`,`Status`,`lastOn` FROM `users` WHERE `Admin` > 0 ORDER BY `Admin` DESC");

$w->execute();

$admins = $w->rowCount();

$c = Config::$g_con->prepare("SELECT `id`,`CChar`,`name`,`Helper`,`Member`,`Status`,`lastOn` FROM `users` WHERE `Helper` > 0 ORDER BY `Helper` DESC");

$c->execute();

$x = Config::$g_con->prepare("SELECT `id`,`CChar`,`name`,`Rank`,`Member`,`Status`,`lastOn` FROM `users` WHERE `Rank` > 6 ORDER BY `Member` DESC");

$x->execute();

$leaders = $x->rowCount();

?>
<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Admins
</p>
</div>
</div>
<table class="table table-dark table-sm table-responsive-lg">
<tbody><tr class="success">
<td>Status</td>
<td>Name</td>
<td>Admin level</td>
<td>Admin options</td>
<td>Last Online</td>
<tbody>
</tr>

</thead>

<?php

while($user = $w->fetch(PDO::FETCH_OBJ)) {

	echo '

	<tr>

		<td>

			<ul class="list-unstyled list-contacts"><div class="media">

            '.($user->Status ? '<span class="badge badge-success">online</span>' : '<span class="badge badge-light">offline</span>').'
			</div></ul>

		</td>

		<td>'.Config::formatName($user->name).'</td>

		<td>'.$user->Admin.'</td>

		<td>';

		$wd = Config::$g_con->prepare('SELECT `ID`,`Color`,`Icon`,`Tag` FROM `wcode_functions` WHERE `UserName` = ?');

		$wd->execute(array($user->name));

		if($wd->rowCount()) {

			$text = '';

			while($r_data = $wd->fetch(PDO::FETCH_OBJ)) $text = $text . '<span class="badge badge-primary" style="background-color: '.$r_data->Color.'"> <i class="'.$r_data->Icon.'"></i> '.Config::xss(Config::clean($r_data->Tag)).'</span> ';

			echo $text;

		}

		echo '</td>

		<td>'.Config::timestamp($a->lastOn).'</td>

	</tr>

	';

}

?>
</tbody></table>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Helpers
</p>
</div>
</div>
<table class="table table-dark table-sm table-responsive-lg">
<tbody><tr class="success">
<td>Status</td>
<td>Name</td>
<td>Staff Points</td>
</tr>
</thead>

				<tbody>

				<?php

				while($user = $c->fetch(PDO::FETCH_OBJ)) {

					echo '

					<tr>

						<td>

							<ul class="list-unstyled list-contacts"><div class="media">


            '.($user->Status ? '<span class="badge badge-success">online</span>' : '<span class="badge badge-light">offline</span>').'

							</div></ul>

						</td>

						<td>'.Config::formatName($user->name).'</td>

						<td>(neterminat)</td>

					</tr>

					';

				}

				?>

</tbody></table>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Leaders
</p>
</div>
</div>
<table class="table table-dark table-sm table-responsive-lg">
<tbody><tr class="success">
<td>Status</td>
<td>Name</td>
<td>Faction</td>
<td>Faction members</td>
<td>Last online</td>
</tr>
</thead>

<tbody>

<?php

while($user = $x->fetch(PDO::FETCH_OBJ)) {

	if($user->Member == 0) continue;

	echo '

	<tr>

		<td>

			<ul class="list-unstyled list-contacts"><div class="media">

            '.($user->Status ? '<span class="badge badge-success">online</span>' : '<span class="badge badge-light">offline</span>').'
			</div></ul>

		</td>

		<td>'.Config::formatName($user->name).'</td>

		<td>'.Config::factionName($user->name,$user->Member).'</td>

		<td>'.Config::factionMembers($user->Member).'/20</td>
	
		<td>'.Config::timestamp($user->lastOn).'</td>

	</tr>

	';

}

?>

</tr><tr>
</tbody></table>
<!--
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
Complaint created last 7 days: <b>411</b><br>
Complaints created last 24h: <b>52</b><br>
Newbie questions asked in the last 7 days: <b>6102</b></div>
</div>
</div>
-->
</div>
</div>