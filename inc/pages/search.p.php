<div class="panel" style="padding: 10px">
<?php
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
if(isset($_POST['search_sub']) && strlen($_POST['search_num'])) {
	$search = $_POST['search_num'];
	$w = Config::$g_con->prepare("SELECT `Model`,`id`,`name`,`Level`,`Member`,`ConnectedTime`,`Status` FROM `users` WHERE `name` LIKE ? LIMIT 50");
	$w->execute(array('%'.$search.'%'));
	if(!$w->rowCount()) { Config::createSN("danger","Nothing could be found by your search criteria!"); Config::gotoPage("search"); }
	else { 
		echo '
		';
		while($user = $w->fetch(PDO::FETCH_OBJ)) {
			echo '		
			<div class="page-content">
			<div class="row-fluid">
			<div class="span12">
	
			<div class="card  bg-purple text-white">
			<div class="card-body">
			<p class="card-title typography-headline">
			Search Player
			</p>
			</div>
			</div>
			<div class="card bg-dark-2 text-white">
			<div class="card-body">
			<div class="card-text">
			<div class="row">
			<div class="col-lg-3">
			<div class="card bg-dark-2 text-white">
			<div class="card-body">
			<div class="card-text">
			<tr>
				<div>
					<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.$user->Model.'.png" style="height: 100px; padding-right: 10px" class="float-left" alt="PericolRPG"></a>
				</div>
				<td>ID: '.Config::formatName($user->id).'</td><br>
				<td>Name: '.Config::formatName($user->name).'</td><br>
				<td>Level: '.$purifier->purify($user->Level).'</td><br>
				<td>Faction: '.Config::factionName($user->name,$user->Member).'</td>
			</tr>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			';
		}
		echo '</tbody></table>';
	}
	setcookie("searchresult", "", time());
} else {
	echo '
	<div class="card  bg-purple text-white">
	<div class="card-body">
	<p class="card-title typography-headline">
	Search Player
	</p>
	</div>
	</div>
	<div class="card bg-dark-2 text-white">
	<div class="card-body">
	<div class="card-text">
	<form method="post">
	<div class="form-group">
	<label for="Username">Player name:</label>
	<input class="form-control" placeholder="Username" type="text" name="search_num"><br>
	<button type="submit" name="search_sub" class="btn btn-primary">Search</button>

	</div>
	</form>
	</div>
	</div>
	</div>
	';
}
?>
</div>