<div class="panel" style="padding: 10px">

<table class="table table-minimal">

	<thead>

		<tr>

			<td><b>Vehicle</b></td>
			<td><b>Vehicle Name</b></td>
			<td><b>Top speed</b></td>
			<td><b>Dealership Price</b></td>			
			<td><b>Stoc dealership</b></td>	

		</tr>

	</thead>

	<tbody>

	<?php
		$q = Config::$g_con->prepare('SELECT * FROM `dsveh` ORDER BY `id` ASC LIMIT 95');
		$q->execute();
		while($row = $q->fetch(PDO::FETCH_OBJ)) { ?>
			<tr>
				<td class="center"><img src="<?php echo Config::$_PAGE_URL ?>assets/img/vehicles/<?php echo $row->Model ?>.jpg" alt="560" title="560" style="width: 105px"/></td>
				<td><?php echo $row->Model ?></td>
				<td><?php echo $row->MaxSpeed ?> km/h</td>
				<td>$<?php echo number_format($row->Price,0,'.','.'); ?></td>
				<td><?php echo $row->Stock ?></td>
			</tr>
	<?php } ?>

	</tbody>

</table>

</div>