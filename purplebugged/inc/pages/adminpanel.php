<?php 
if(!Config::isAdmin(Config::getUser())) Config::gotoPage("",0);
?>
<div class="panel" style="padding: 10px">
	<ul class="nav nav-tabs" role="tablist">
		<li class="active"><a href="#home" role="tab" data-toggle="tab"><i class="fa fa-legal"></i> Administrative</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="home">
			<i class="fa fa-info-circle"></i> Welcome to AdminCP.
		</div>
		<?php if(Config::isAdmin(Config::getUser(), 6)) { ?>
<?php 		
if(isset($_POST['update_hours'])) 
{ if(is_numeric($_POST['hours'])) 
{ $p = Config::$g_con->prepare("UPDATE `wcode_editables` SET `Text` = ? WHERE `Form` = 'HoursHelper'"); 
$p->execute(array($_POST['hours'])); } }
?>	
<?php	
if(isset($_POST['turnon'])) { 
$k = Config::$g_con->prepare("UPDATE `wcode_editables` SET `Text` = 0 WHERE `Form` = 'StatusHelper'"); 
$k->execute(array()); 
}

if(isset($_POST['turnoff'])) { 
$k = Config::$g_con->prepare("UPDATE `wcode_editables` SET `Text` = 1 WHERE `Form` = 'StatusHelper'"); 
$k->execute(array()); 
}

if(isset($_POST['add'])) { 
$w = Config::$g_con->prepare("INSERT INTO `kenny_hquestions` (`Question`,`Type`) VALUES (?, ?)"); 
$w->execute(array(Config::xss(Config::clean($_POST['text'])),1)); 
}

if(isset($_POST['delete'])) { 
$w = Config::$g_con->prepare("DELETE FROM `kenny_hquestions` WHERE `id` = ?"); 
$w->execute(array($_POST['delete'])); 
}

if(isset($_POST['edit'])) { 
	$w = Config::$g_con->prepare("UPDATE `kenny_hquestions` SET `Question` = ? WHERE `ID` = ?"); 
	$w->execute(array($_POST['question'.$_POST['edit'].''],$_POST['edit'])); 
}	
?>
<?php 
$k3 = Config::$g_con->prepare('SELECT * FROM `wcode_editables` WHERE `Form` = "StatusHelper"');  
$k3->execute(array());
$statushelper = $k3->fetch(PDO::FETCH_OBJ); 
?>	

<?php 
$k4 = Config::$g_con->prepare('SELECT * FROM `wcode_editables` WHERE `Form` = "HoursHelper"');  
$k4->execute(array());
$hourshelper = $k4->fetch(PDO::FETCH_OBJ); 
?>	
		
<div class="col-md-9">
	<div class="panel" style="padding: 10px">
		<h5>Manage the questions for Helper Applications</i></h5><hr>
		<form method="post">
			<div class="input-group">
				<input class="form-control" type="text" name="text">
				<span class="input-group-btn">
					<button class="btn btn-primary" type="submit" name="add"><i class="fa fa-plus-square"></i></button>
				</span>
			</div><br>
			<i class="fa fa-info-circle"></i> Question will appear exactly in same order as here!
			<hr>
			<?php
			$k = Config::$g_con->prepare("SELECT * FROM `kenny_hquestions` WHERE `Type` = 1");
			$k->execute();
			while($question = $k->fetch(PDO::FETCH_OBJ)) {
				echo '
				<div class="input-group" style="margin-bottom: 5px">
					<input class="form-control" type="test" name="question'.$question->ID.'" value="'.$question->Question.'">
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit" name="edit" value="'.$question->ID.'"><i class="fa fa-edit"></i></button>
						<button class="btn btn-primary" type="submit" name="delete" value="'.$question->ID.'"><i class="fa fa-trash"></i></button>
					</span>
				</div>
				';
			}
			?>
		</form>
	</div>
</div>	

<div class="col-md-3">
	<div class="panel" style="padding: 10px">
		<h5>Helper Applications</h5>
		<form method="post">
<?php if ($statushelper->Text == 1) { ?>		
<button type="submit" name="turnon" class="btn btn-success btn-outline">Turn ON</button>
<?php } else { ?>
<button type="submit" name="turnoff" class="btn btn-danger btn-outline">Turn OFF</button>
<?php } ?>
			<hr>
			<div class="input-group">
				<input class="form-control" type="number" name="hours" placeholder="Hours">
				<span class="input-group-btn">
					<button class="btn btn-primary" type="submit" name="update_hours">Update</button>
				</span>
			</div>
		</form>
	</div>
</div>	
		<?php } ?>
	</div>
	<div class="clearfix"></div>
</div>