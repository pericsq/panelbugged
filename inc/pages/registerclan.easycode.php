<?php  
if(isset($_POST['register_clan'])) {
    if(strlen($_POST['name']) && strlen($_POST['Owner']) && strlen($_POST['Tag']))
    {
      $s = Config::$g_con->prepare('INSERT INTO `clans` SET `Name` = ?, `Owner` = ?, `Tag` = ?');
      $s->execute(array($_POST['Name'],$_POST['Owner'],$_POST['Tag']));
      Config::gotoPage("clans", 0, "success", "Clan tau a fost adaugat, Cu succes");
    }
  }
?>
<div class="page-content">
<div class="row-fluid">
<div class="span12">

<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Register a Clan
</p>
</div>
</div>
<div class="alert alert-danger">
Registering a clan costs 100 premium points for a 6 month period. After that you will have to pay 20 premium points / month to renew it!<br>
Inregistrarea unui clan costa 100 puncte premium / 6 luni. Dupa aceasta perioada, prelungirea clanului va costa 20 puncte premium / luna!<br><br>
Taking the name of another well-known clan that is not registered is strictly forbidden. Your clan will get deleted. Don't register clans like ZEW, St., [AIM] if you are not the founder of that clan.<br>
Inregistrarea numelui unui alt clan cunoscut care nu este inregistrat este interzisa. Clanul tau poate fi sters. Nu inregistra clanuri ca ZEW, St., [AIM] daca nu esti fondatorul clanului.<br><br>
Nu inregistrati clanuri care sunt inregistrate pe un alt server de SA:MP detinut de comunitatea bugged!
</div>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Clan Information
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<h4>RO</h4>
<ul>
<li>Clanul iti aduce un chat in joc (/c) si optiunea de a putea invita playeri in clan.</li>
<li>Se pot modifica numele rank-urilor pentru clanuri (ce apar in chat) si culoarea pentru chatul clanului.</li>
<li>Exista o limita de 25 membri pentru fiecare clan.</li>
<li>Clanurile nu au HQ-uri, masini pt clan, war-uri intre clanuri. Clanurile sunt o metoda de a comunica mai usor cu un grup de playeri.</li>
<br>
<li>Este interzis sa postati orice legat de piraterie in forumul clanului (link-uri download filme, muzica, jocuri, crack-uri)</li>
<li>Este interzis sa folositi forumul pentru a jigni alti playeri / clanuri / factiuni</li>
<li>Regulile generale ale forumului se aplica si in forumurile clanurilor</li>
<li>Alegeti un nume normal pentru clan. Nu folositi numele altor clanuri. Nu folositi cuvinte vulgare.</li>
<li>Nerespectarea regulilor poate duce la stergerea clanului</li>
<li>Vanzarea clanurilor este interzisa!</li>
<li>Cererea de bani pentru acceptarea in clan/rank up este interzisa</li>
<li>Playerii ce folosesc clanurile pentru inselatorii (cererea de bani/masini pentru acceptarea in clan, cererea de bani imprumut pentru acceptarea in clan) vor fi banati permanent si vor avea clanul sters</li>
<li>Clanurile nu pot avea blacklist. Omorarea unui alt player care e pe blacklist se sanctioneaza ca DM.</li>
<li>Clanurile nu sunt mafii. Nu va fi implementat vreodata sistem de war-uri intre clanuri.</li>
<li>Pentru a lasa alt player sa fie liderul clanului, acel player trebuie sa aiba 30 zile in clan.</li>
<li>Nu inregistrati clanuri cu promoveaza cheat-urile in vreun fel (ex: clanul codatilor, clanul aimbot, clanul norecoil)</li>
</ul>
<h4>EN</h4>
<ul>
<li>By registering a clan, you will get a clan chat (/c) and you will be able to invite people into your clan</li>
<li>There is a limit of 25 members for every clan</li>
<li>The clan owner will be able to modify rank names and the chat color for the clan chat(/c).</li>
<br>
<li>You're not allowed to post anything warez-related on the forums (download links for torrents, mp3s, videos, games, software, cracks)</li>
<li>It's forbidden to use the forum just to insult other players/clans/factions</li>
<li>The forum rules also apply on clan subforums</li>
<li>Choose a unique name. Don't copy the name of other clans.</li>
<li>Not respecting the rules could lead to your clan being removed</li>
<li>You're not allowed to sell your clan.</li>
<li>You're not allowd to use the clan for scams. Your clan will be removed and you will get banned.</li>
<li>You're not allowed to ask for money to accept someone to join your clan.</li>
<li>To make someone else the owner of the clan, that person will have to in the clan for more than 30 days.</li>
</ul>
</div>
</div>
</div>
<br>
<div class="card  bg-purple text-white">
<div class="card-body">
<p class="card-title typography-headline">
Registration
</p>
</div>
</div>
<div class="card bg-dark-2 text-white">
<div class="card-body">
<div class="card-text">
<form method="POST">
    <div class="form-group">
        <input id="Name" type="Name" name="Name"  class="form-control" placeholder="Clan name">
    </div>
    <div class="form-group">
        <input id="Owner" type="Owner" name="Owner"  class="form-control" placeholder="Your username">
    </div>
    <div class="form-group">
        <input id="Tag" type="Tag" name="Tag" class="form-control" placeholder="Clan Tag (ex: [TAG], Tag., _Tag, .Tag)">
    </div>
    <br>
    <button type="submit" name="register_clan" class="btn btn-primary btn-lg active">Register clan</button>
    </div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
