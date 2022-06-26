<?php
if(Config::isLogged()) Config::gotoPage("", 0);

if(isset($_POST['login_submit']) && !Config::isLogged()) {
  if(strlen($_POST['username']) && md5($_POST['password']))
  {
    $wcodero = Config::$g_con->prepare('SELECT `id`,`id` FROM `users` WHERE `name` = ? AND `password` = ?');
    $wcodero->execute(array($_POST['username'], md5($_POST['password'])));
    $wcodero->execute(array($_POST['username'], hash('sha256', $_POST['password'])));
    if($wcodero->rowCount()) {
      $account = $wcodero->fetch(PDO::FETCH_OBJ);
      echo'';
     echo '<meta http-equiv="refresh" content="0">';
      $_SESSION['account_panel'] = $account->id;
    }
    else echo '<script> 
     swal("", "Nume sau parola incorecta!", "error");
    </script>';
  }
}
?>

<div class="col-xl-4 mx-auto login">
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Log in
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<form method="post"> 
<div class="form-group">
Username:<br>
<input type="text" name="username" class="form-control">
<br>Password:<br>
<input type="password" name="password" class="form-control">
<br>
<input type="submit" name="login_submit" value="login" class="btn btn-inverse">
</div>
</form>
Forgot your password? Click <a href="<?php echo Config::$_PAGE_URL; ?>validate">here</a>!
</div>
</div>
</div>
</div>
