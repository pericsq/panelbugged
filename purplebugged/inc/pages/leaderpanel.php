<?php
if(!Config::isLogged()) { Config::gotoPage("",0,"danger","You must be logged in!"); }
else if(Config::getData("users","Member",Config::getUser()) == 0 || Config::getData("users","Rank",Config::getUser()) < 6) { Config::gotoPage("",0,"danger","You must be leader to can access this page!"); }
$faction = Config::getData("users","Member",Config::getUser());
if(isset($_POST['update_lvl'])) { if(is_numeric($_POST['levl'])) { $p = Config::$g_con->prepare("UPDATE `factions` SET `MinLevel` = ? WHERE `ID` = ?"); $p->execute(array($_POST['levl'],$faction)); } }
if(isset($_POST['turnon'])) { $p = Config::$g_con->prepare("UPDATE `factions` SET `App` = 0 WHERE `ID` = ?"); $p->execute(array($faction)); }
if(isset($_POST['turnoff'])) { $p = Config::$g_con->prepare("UPDATE `factions` SET `App` = 1 WHERE `ID` = ?"); $p->execute(array($faction)); }
if(isset($_POST['add'])) { $w = Config::$g_con->prepare("INSERT INTO `wcode_questions` (`question`,`factionid`) VALUES (?, ?)"); $w->execute(array(Config::xss(Config::clean($_POST['text'])),$faction)); }
if(isset($_POST['delete'])) { $w = Config::$g_con->prepare("DELETE FROM `wcode_questions` WHERE `id` = ?"); $w->execute(array($_POST['delete'])); }
if(isset($_POST['edit'])) { 
	$w = Config::$g_con->prepare("UPDATE `wcode_questions` SET `question` = ? WHERE `id` = ?"); 
	$w->execute(array($_POST['question'.$_POST['edit'].''],$_POST['edit'])); 
}
?>
<div class="col-md-9">
	<div class="panel" style="padding: 10px">
		<h5>Manage the questions for <i><?php echo Config::justFactionName($faction) ?></i></h5><hr>
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
			$w = Config::$g_con->prepare("SELECT * FROM `wcode_questions`");
			$w->execute();
			while($question = $w->fetch(PDO::FETCH_OBJ)) {
				echo '
				<div class="input-group" style="margin-bottom: 5px">
					<input class="form-control" type="test" name="question'.$question->id.'" value="'.$question->question.'">
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit" name="edit" value="'.$question->id.'"><i class="fa fa-edit"></i></button>
						<button class="btn btn-primary" type="submit" name="delete" value="'.$question->id.'"><i class="fa fa-trash"></i></button>
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
		<h5>Applications</h5>
		<form method="post">
			<?php if(Config::getFullData("factions","App","ID",$faction)) { 
				echo '<button type="submit" name="turnon" class="btn btn-success btn-outline">Turn ON</button>';
			} else {
				echo '<button type="submit" name="turnoff" class="btn btn-success btn-outline">Turn OFF</button>';
			} ?>
			<hr>
			<div class="input-group">
				<input class="form-control" type="text" name="levl" placeholder="Level: <?php echo Config::getFullData("factions","MinLevel","ID",$faction) ?>">
				<span class="input-group-btn">
					<button class="btn btn-primary" type="submit" name="update_lvl">Update</button>
				</span>
			</div>
		</form>
	</div>
</div>
