<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Introducere date personale
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
Pentru a continua plata, trebuie sa introduci cateva date personale.<br>Datele vor fi private. Vor putea fi vizualizate doar de tine si proprietarii site-ului.<br>Prin trimiterea acestui formular confirmi pe proprie raspundere ca datele furnizate sunt reale.<br><br>(!) COMPLETAREA ACESTUI FORMULAR CU DATE FALSE VA DUCE LA SUSPENDAREA CONTULUI!
<hr>
<form method="POST" action="<?php echo Config::$_PAGE_URL; ?>date" accept-charset="UTF-8"><input name="_token" type="hidden" value="cFAhwaSouY3xJth98vS226HsFuJrCMbdTwmPblRm">
<div class="form-group">
<label for="name">Nume si prenume:</label>
<input class="form-control" placeholder="ex: Popescu Ionut" pattern="[a-zA-Z\s]+" name="name" type="text" id="name">
<br>
<label for="judet">Judet:</label>
<select name="judet" class="form-control">
<option value="0">Neselectat</option>
<option value="40">Bucuresti</option>
<option value="1">Alba</option>
<option value="2">Arad</option>
<option value="3">Arges</option>
<option value="4">Bacau</option>
<option value="5">Bihor</option>
<option value="6">Bistrita-Nasaud</option>
<option value="7">Botosani</option>
<option value="8">Brasov</option>
<option value="9">Braila</option>
<option value="10">Buzau</option>
<option value="11">Caras-Severin</option>
<option value="51">Calarasi</option>
<option value="12">Cluj</option>
<option value="13">Constanta</option>
<option value="14">Covasna</option>
<option value="15">Dambovita</option>
<option value="16">Dolj</option>
<option value="17">Galati</option>
<option value="52">Giurgiu</option>
<option value="18">Gorj</option>
<option value="19">Harghita</option>
<option value="20">Hunedoara</option>
<option value="21">Ialomita</option>
<option value="22">Iasi</option>
<option value="23">Ilfov</option>
<option value="24">Maramures</option>
<option value="25">Mehedinti</option>
<option value="26">Mures</option>
<option value="27">Neamt</option>
<option value="28">Olt</option>
<option value="29">Prahova</option>
<option value="30">Satu Mare</option>
<option value="31">Salaj</option>
<option selected="selected" value="32">Sibiu</option>
<option value="33">Suceava</option>
<option value="34">Teleorman</option>
<option value="35">Timis</option>
<option value="36">Tulcea</option>
<option value="37">Vaslui</option>
<option value="38">Valcea</option>
<option value="39">Vrancea</option>
</select>
<br>
<label for="location">Adresa completa:</label>
<input class="form-control" pattern="[a-zA-Z0-9\s\.\,\-]+" placeholder="ex: Craiova, judet Dolj, str. ZZZZZ, nr. 22" name="location" type="text" id="location">
<label for="phone">Nr telefon:</label>
<input class="form-control" name="phone" type="text" id="phone">
<label for="email">Email (optional):</label>
<input class="form-control" name="email" type="text" id="email">
<br>
<input class="btn btn-success input-xlarge" type="submit" value="Continua">
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>