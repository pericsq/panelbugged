<?php
$c = Config::$g_con->prepare("SELECT * FROM `banlog` ORDER BY `ID` DESC ".Config::limit()."");
$c->execute();
?>
<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Bans
</p>
</div>
</div>
<table class="table table-dark">
<tbody><tr class="info">
<td>Player</td>
<td>Level</td>
<td>Ban Date</td>
<td>Ban Reason</td>
<td>Banned by</td>
<td>Ban Expire at</td>
<td>IP Ban</td>
</tr>
</tbody>
		<tbody>
		<?php
		while($ban = $c->fetch(PDO::FETCH_OBJ)) {
			echo '
			<tr>
				<td>'.Config::formatName($ban->player).'</td>
				<td>'.$ban->Level.'</td>
				<td>'.($ban->day ? '<span class="label label-success label-transparent">'. $ban->day . ' days</span>' : '<span class="label label-danger label-transparent">Permanent</span>').'</td>
				<td>'.Config::xss(Config::clean($ban->reason)).'</td>
				<td>'.Config::formatName($ban->admin).'</td>
				<td>'.$ban->time.' ('.Config::timeAgo($ban->time).')</td>
				<td>'.$ban->ip.'</td>
			</tr>
			';
		}
		?>
		</tbody>
	</table><br>
	<?php echo Config::create(Config::rows("banlog","ID")) ?>
</div>
</div>
</div>