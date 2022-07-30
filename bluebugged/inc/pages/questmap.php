<div id="fb-root"></div>

<script>(function(d, s, id) {

  var js, fjs = d.getElementsByTagName(s)[0];

  if (d.getElementById(id)) return;

  js = d.createElement(s); js.id = id;

  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.1';

  fjs.parentNode.insertBefore(js, fjs);

}(document, 'script', 'facebook-jssdk'));</script>

<?php

$w = Config::$g_con->prepare('SELECT `id` FROM `users`'); $w->execute(); $users = $w->rowCount();

$w = Config::$g_con->prepare('SELECT `id` FROM `users` WHERE `Status` = 1'); $w->execute(); $online = $w->rowCount();

$w = Config::$g_con->prepare('SELECT `ID` FROM `houses`'); $w->execute(); $houses = $w->rowCount();

$w = Config::$g_con->prepare('SELECT `ID` FROM `houses` WHERE `Owner` != "AdmBot"'); $w->execute(); $h_owned = $w->rowCount();

$w = Config::$g_con->prepare('SELECT `ID` FROM `bizz`'); $w->execute(); $bizz = $w->rowCount();

$w = Config::$g_con->prepare('SELECT `ID` FROM `cars`'); $w->execute(); $cars = $w->rowCount();

$w = Config::$g_con->prepare('SELECT `ID` FROM `bizz` WHERE `Owner` != "The State"'); $w->execute(); $b_owned = $w->rowCount();

?>

<div class="page-content">
<div class="row-fluid">
<div class="span12">
<div class="alert alert-danger">You need to find at least 33 hidden objects in order to see the map. / Trebuie sa gasesti 33 obiecte ascunse inainte de a putea vedea harta de pe user panel. (0/33)</div>
<div class="row">
<div class="col-lg-6">
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Server Info
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<div class="row">
<div class="col-lg-6">
<a href="easycode/online.html">
<img src="https://bluepanel.bugged.ro/storage/online_small.jpeg?dontCache=1438" width="450" height="450" alt="Live Map" title="Live Player Map" class="img-fluid rounded"></a>
</div>
<div class="col-lg-6">
<ul class="list-group">
<li class="list-group-item list-group-item-dark2">
<i class="material-icons">directions_run</i>
<a href="easycode/online.html" class="">Jucatori conectati: <?php echo $online; ?></a>
</li>
<li class="list-group-item list-group-item-dark2">
<i class="material-icons">group</i>
<a href="easycode/online.html" class="">Jucatori inregistrati: <?php echo $users; ?></a>
</li>
<li class="list-group-item list-group-item-dark2">
<i class="material-icons">graphic_eq</i>
<a href="easycode/online.html" class="">Conectati sapt. trecuta: x</a>
</li>
<li class="list-group-item list-group-item-dark2">
<i class="material-icons">directions_car</i>
<a href="easycode/vehicles.html" class="">Vehicule Personale: <?php echo $cars; ?></a>
</li>
<li class="list-group-item list-group-item-dark2">
<i class="material-icons">home</i>
<a href="easycode/houses.html" class="">Case: <?php echo $houses; ?></a>
</li>
<li class="list-group-item list-group-item-dark2">
<i class="material-icons">business</i>
<a href="easycode/businesses.html" class="">Afaceri: <?php echo $bizz; ?></a>
</li>
</ul>
<br>
</div>
</div>
</div>
</div>
</div>
<br>
</div>

<div class="col-lg-6">
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
What's New
</p>
</div>
</div>
<ul class="list-unstyled list-contacts">
<?php
if(isset($_POST['delete_ac']) && Config::isAdmin(Config::getUser())) {
$w = Config::$g_con->prepare('DELETE FROM `faction_logs` WHERE `id` = ?');
$w = Config::$g_con->prepare('DELETE FROM `faction_logs` WHERE `player` = ?');
$w->execute(array($_POST['delete_ac'])); 
echo Config::createSN("success","Faction history line #".$_POST['delete_ac']." has been deleted!");
}
$wcs = Config::$g_con->prepare('SELECT * FROM `faction_logs` ORDER BY `id` DESC LIMIT 10');
$wcs->execute();
while($fhistory = $wcs->fetch(PDO::FETCH_OBJ)) {
echo '
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<div class="media">
<img class="mr-3 rounded-circle" src="'.Config::$_PAGE_URL.'assets/img/avatars/1.png" alt="'.Config::xss(Config::clean($fhistory->player)).'" width="40" height="40">
<div class="media-body">
<p class="mt-0">
<a href="'.Config::$_PAGE_URL.'profile/'.Config::xss(Config::clean($fhistory->player)).'">
</a>
'.Config::xss(Config::clean($fhistory->Text)).'.
<span class="float-right text-white-50 created_at">'.Config::timeAgo($fhistory->time).'</span>
</p>
</div>
</div>
</div>
</div>
</div>';
echo '</li>';
}
?>
</form>
</ul>
<br>
</div>
</div> 
</div>
</div>
</div>
<script src="<?php echo Config::$_PAGE_URL ?>assets/vendor/jquery/jquery.min.js"></script>

<script>

$(function()

{

	$('#mini-bar-chart').sparkline('html',

	{

		type: 'bar',

		barWidth: 8,

		height: 45,

		barColor: '#72BB23',

		chartRangeMin: 0,

		chartRangeMax: 100

	});

});

</script>

