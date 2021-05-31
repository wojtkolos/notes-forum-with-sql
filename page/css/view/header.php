<!DOCTYPE html>
<html>
<head>
    <title>Zadanie 7 - WWW i jzyki skryptowe</title>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <link rel="stylesheet" type="text/css" href="css/style7.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="activity.js"></script>
	<script src="skr.js"></script>
    
</head>
<body>
<header>
  <h1 class="logo">
      Zadanie 7
  </h1>
  <h2 class="logo">
      Projektowanie aplikacji do zbierania i prezentacji danych
  </h2>
</header>
<nav class="navbar">
        <a href="../">Home</a>
        <?php for($n=1;$n<=10;$n++) { if( is_dir("../zadanie".$n) ) { ?>
        <a href="../zadanie<?=$n?>">Zadanie <?=$n?></a>
        <?php } } ?>
</nav>