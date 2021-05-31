<section class="user-info"><?php if(isset($this->u) and $this->u != false and $this->u['userlevel']==20){ ?>
<div>
<?php if($this->context=="userlist" or $this->context=="activity"){?><a href="?cmd=topics">Tematy</a><?php } ?>
<?php if($this->context=="topics" or $this->context=="activity"){?><a href="?cmd=userlist">Lista uczestników</a><?php } ?>
<?php if($this->context=="topics" or $this->context=="userlist"){?><a href="?cmd=activity">Lista aktywności</a><?php } ?>
</div>
<?php if($this->u != false and $this->u['userlevel']==20 and $this->context=="topics"){ ?>
<div><a href="" id="addtopic" >+ Dodaj notatkę</a></div>
<?php } ?>

<?php } ?>
<?php
if(isset($this->g) and $this->g != false){
?>
Zalogowany jako: <?=$this->g['username'];?>
<?php } else {?>
Zalogowany jako: <?=$this->u['username'];?> (<?=$this->u['userid'];?>) <a href="?cmd=logout" >WYLOGUJ</a>
 <?php } ?>




<?php if(isset($_SESSION['userlist']) and $_SESSION['userlist'] == true and $this->g == false){ ?>
    
<br />
<table><tr><th>Identyfikator</th><th>Nazwa</th><th>Poziom</th><th></th></tr>
<?php foreach($users as $k=>$v){ ?>
<tr>
<td><?=$v['userid']?></td>
<td><?=$v['username']?></td>
<td><?=($v['userlevel']==20)?'admin':'user';?></td>
<td><?php if($v['userid']!='admin'){ ?>
<a href="?cmd=changeuser&userid=<?=$v['userid']?>">Zmień</a>&nbsp;
<a href="?cmd=deluser&userid=<?=$v['userid']?>">Kasuj</a>
<?php } ?></td>
</tr>
<?php } ?>
</table>
<?php } ?>
</section>
