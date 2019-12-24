<html>
<head>
<meta http-equiv="refresh" content="5; url=showmsg.php">
</head>
<body>
<?php
  session_start();
  $id = session_id();
  include "functions.php";
  echo "<div style='color:red; border:1px solid red; padding:0.5em; margin:0.5em'>On-line ".onLine()." человек(а)</div>\n";
  $file = file("messages.txt");
  if ( count( $file ) > 0 ) {
    $file = array_reverse( $file );
    $messages = "";
    foreach($file as $value) {
	  $record = explode("¤", trim($value));
      $messages = $messages."<div>Добавил(а): <b>".$record[0]."</b> <span style='color:green'>".
      $record[1]."</span><br/>".$record[2]."<hr></div>";
    }
    $messages = make_smiles($messages);
  } else {
    $messages = "<div>Нет сообщений</div>\n";
  }
  echo $messages; 
?>
</body>
</html>