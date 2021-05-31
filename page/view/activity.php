
<section id="user-activity" class="user-info">
<br />

<input type="text" id="myInput" onkeyup="searchEng()" placeholder="Szukaj po identyfikatorze">
<input type = "hidden" id="myDate" onkeyup="searchEng()" placeholder="Data końcowa" >
<input type="radio" name="myRadios" onclick="handleClick(this);" value="1" checked="checked" /> Identyfikator
<input type="radio" name="myRadios" onclick="handleClick(this);" value="2" /> IP
<input type="radio" name="myRadios" onclick="handleClick(this);" value="3" /> Rodzaj aktywności
<input type="radio" name="myRadios" onclick="handleClick(this);" value="4" /> Data


<table id="activity-list">
<thead>
<tr><th>Identyfikator</th><th>IP</th><th>URI</th><th>Data</th><th>Id</th><th></th></tr>
</thead>
<tbody>
<?php foreach($activity as $k=>$v){ ?>
  <tr><td><?=$v['userid']?></td>
  <td><?=$v['IP']?></td>
  <td><?=$v['URI']?></td>
<td><?=$v['date']?></td>
<td><?=$v['id']?></td>
</tr>
<?php } ?>
</tbody>
</table>



