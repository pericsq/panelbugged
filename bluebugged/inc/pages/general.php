<?php echo '
<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
IMPORTANT
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
Inainte de a crea tichetul, citeste asta: <br>
Daca ai o problema legata de server, poti deschide un tichet aici.<br>
Daca ai o problema cu un cont de pe forum, specifica numele contului de pe forum.<br>
</div>
</div>
</div>
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Deschide tichet ajutor
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<form method="POST" action="tickets/create" accept-charset="UTF-8">
<div class="form-group">
<label for="text">Tip tichet: </label>
<b>Tichet general</b>
<a href="tickets"><i class="material-icons">backspace</i></a>
<br>
<br>
<label for="text">Detalii despre problema: </label>
<textarea class="form-control" rows="5" type="text" value="Others" name="ticket"  cols="50" id="text"></textarea>
<br>
<input class="btn btn-small btn-danger" type="submit" name="submit" value="Deschide tichet">
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>';
?>