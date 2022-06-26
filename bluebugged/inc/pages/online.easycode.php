<?php
$w = Config::$g_con->prepare("SELECT `Model`,`name`,`Level`,`Member`,`ConnectedTime`,`Status`,`Job` FROM `users` WHERE `Status` = 1");
$w->execute();
?>
<div class="panel" style="padding: 10px">
<table class="table table-minimal">
	<thead>
		<tr>
			<th>#</th>
			<th>USER</th>
			<th>LEVEL</th>
			<th>FACTION</th>
			<th>CONNECTED TIME</th>
		</tr>
	</thead>
	<tbody>
	<?php
	while($user = $w->fetch(PDO::FETCH_OBJ)) {
		echo '
		<tr>
			<td>
				<ul class="list-unstyled list-contacts"><div class="media">
					<img src="'.Config::$_PAGE_URL.'assets/img/avatars/'.$user->Model.'.png" class="picture" alt="" style="border: 2px solid #79afbe">
					<span class="status '.($user->Status ? 'online' : '').'"></span>
				</div></ul>
			</td>
			<td>'.Config::formatName($user->name).'</td>
			<td>'.$user->Level.'</td>
			<td>'.Config::factionName($user->name,$user->Member).'</td>
			<td>'.$user->ConnectedTime.'</td>
		</tr>
		';
	}
	?>
	</tbody>
</table>
</div>