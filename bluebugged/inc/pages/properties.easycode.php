<div class="panel" style="padding: 10px">
<?php
if(isset(Config::$_url[1]) && Config::$_url[1] == "houses") {
?>
<table class="table table-minimal">
	<thead>
		<tr>
			<th>#</th>
			<th>OWNED</th>
			<th>LEVEL</th>
			<th>NAME</th>
			<th>PRICE</th>
			<th>MAP</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$jj = Config::$g_con->prepare("SELECT * FROM `houses` ".Config::limit()."");
	$jj->execute();
	while($house = $jj->fetch(PDO::FETCH_OBJ)) {
		echo '<tr>
			<td>'.$house->ID.'</td>
			<td>'.Config::formatName($house->Owner).'</td>
			<td>'.$house->Level.'</td>
			<td>'.$house->Discription.'</td>
			<td>'.Config::formatNumber($house->Value).'</td>
			<td>
				'; ?>
					<a onclick = "morty_ey(<?php echo $house->houseX; ?>,<?php echo $house->houseY; ?>)"><i class="fa fa-map-marker"></i></a>
				<?php
				echo '
			</td>
		</tr>';
	}
	?>
	</tbody>
</table><br>
<?php echo Config::create(Config::rows("houses","ID")) ?>
<?php
} else if(isset(Config::$_url[1]) && Config::$_url[1] == "businesses") { ?>
<table class="table table-minimal">
	<thead>
		<tr>
			<th>#</th>
			<th>OWNED</th>
			<th>LEVEL</th>
			<th>NAME</th>
			<th>PRICE</th>
			<th>MAP</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$jj = Config::$g_con->prepare("SELECT * FROM `bizz` ".Config::limit()."");
	$jj->execute();
	while($bizz = $jj->fetch(PDO::FETCH_OBJ)) {
		echo '<tr>
			<td>'.$bizz->ID.'</td>
			<td>'.Config::formatName($bizz->Owner).'</td>
			<td>'.$bizz->LevelNeeded.'</td>
			<td>'.$bizz->Message.'</td>
			<td>'.Config::formatNumber($bizz->BuyPrice).'</td>
			<td>
				'; ?>
					<a onclick = "morty_ey(<?php echo $bizz->EntranceX; ?>,<?php echo $bizz->EntranceY; ?>)"><i class="fa fa-map-marker"></i></a>
				<?php
				echo '
			</td>
		</tr>';
	}
	?>
	</tbody>
</table><br>

<?php echo Config::create(Config::rows("bizz","ID"));
 } else Config::gotoPage("",0); ?>
</div>
 
<script src="<?php echo Config::$_PAGE_URL; ?>assets/js/map.min.js"></script>