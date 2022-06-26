<div class="panel" style="padding: 10px">
<?php
if(!Config::isAdmin(Config::getUser())) Config::gotoPage("",0);
?>
<table class="table table-minimal">
	<thead>
		<tr>
			<th>#</th>
			<th>ADMIN</th>
			<th>LOG</th>
			<th>TARGET</th>
			<th>DATE</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$jj = Config::$g_con->prepare("SELECT * FROM `wcode_logs` ORDER BY `ID` DESC ".Config::limit()."");
	$jj->execute();
	while($logs = $jj->fetch(PDO::FETCH_OBJ)) {
		echo '<tr>
			<td>#'.$logs->ID.'</td>
			<td>'.Config::formatName($logs->UserName).'</td>
			<td>'.$logs->Log.'</td>
			<td>'.Config::formatName($logs->VictimName).'</td>
			<td>'.$logs->Date.'</td>
		</tr>';
	}
	?>
	</tbody>
</table><br>

<?php echo Config::create(Config::rows("wcode_logs","ID")); ?>
</div>