<div class="panel" style="padding: 10px">

<?php if(!isset(Config::$_url[1])) { 

$w = Config::$g_con->prepare("SELECT `CChar`,`name`,`Level`,`Member`,`ConnectedTime` , `Premium`, `Admin`, `Respect`,`Status` FROM `users` ORDER BY `ConnectedTime` DESC LIMIT 100");

$w->execute();

?>

<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Top Players
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<br>
<div class="tab-content" id="justifiedTabContent">
<div aria-labelledby="home-tab" class="tab-pane fade show active" id="home" role="tabpanel">
<table class="table table-hover table-dark table-sm table-responsive-sm">
<tbody><tr class="">
<td>#</td>
<td>Name</td>
<td> </td>
<td>Level</td>
<td>Playing hours</td>
<td>Respect points</td>
</tr>
</tbody>
	<?php $id = 1;

	while($user = $w->fetch(PDO::FETCH_OBJ)) {

		echo '

		<tr>

			<td>'.$id.'</td>
			<td><a>'.Config::formatName($user->name).'</a></td>
						<td>';
						if($user->Admin) echo '  <i class="material-icons text-danger" title="Admin" class="material-icons text-warning" >security</i>';
						if($user->Helper) echo '<i class="material-icons text-info" title="Helper">security</i>';
						if($user->Premium) echo '<i class="material-icons text-warning" title="Premium">local_parking</i>';
						if($user->Vip == 1) echo '<i class="material-icons text-warning" title="Legend">star</i>';
						echo '</td>
						<td>'.$user->Level.'</td>
						<td>'.$user->ConnectedTime.'</td>
						<td>'.$user->Respect.'</td><td>
						</tr>

		';

		$id++;

	}

	?>

</tbody></table>
</div>
</div>
</div>
</div>
</div>
 
</div>
</div>
</div>

<?php } else { 

	

} ?>

</div>