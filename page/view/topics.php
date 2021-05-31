<section>

<form class="keyWord" action="<?=$this->baseurl?>" method="post">
<input type="text" name="keyWord" placeholder="Szukaj w swoich notatkach" \>
<button type="submit" >Szukaj</button>
</form>

<?php if( !$topics ){ ?>
  <p>Ta nie ma jeszcze żadnych notatek!</p>
<?php }else{ foreach($topics as $k=>$v){ ?>

  <article class="topic">
    <header> </header>
    <div><a href="#" topicid="<?=$k?>" class="topicview"><?=htmlentities($v['topic'])?></a></div>
    <footer>
    <?php if($this->u != false and $this->u['userlevel']==20) { ?>
    <nav>
    <a href="#" topicid="<?=$k?>" class="topicedit">EDYTUJ</a>
    <a href="?id=<?=$v['topicid']?>&cmd=topicdelete">KASUJ</a>
    </nav>
    <?php } ?>
    ID: <?=$v['topicid']?>, Autor: <?=htmlentities($users[$v['userid']]['username'])?>, Utworzono: <?=$v['date']?>
    </footer>
  </article>

<?php } } 
?>
<div class="modal" id="modal_topic">
  <form action="<?=$this->baseurl?>" method="post">
     <a name="topic_form"></a>
     <header><h2><?=($topic)? "Edytuj notatkę" : "Dodaj nową notatkę"?></h2></header>  
     <input type="text" name="topic" placeholder="Temat" autofocus value="<?=($topic)?$topic['topic']:""?>"\><br />
     <textarea name="topic_body" cols="80" rows="10" placeholder="Notatka" ><?=($topic)?$topic['topic_body']:""?></textarea><br />
     <input type="hidden" name="username" value="<?=$user['username'];?>" \>
     <input type="hidden" name="topicid" value="<?=($topic)?$topic['topicid']:"";?>" \>
     <button type="submit" >Zapisz</button>
  </form>
    </div>
</section>
