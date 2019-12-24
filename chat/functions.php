<?php
function make_smiles($str) {
  $smiles[0] = "rolleyes";
  $smiles[1] = "wink";
  $smiles[2] = "cheesy";
  $smiles[3] = "grin";
  $smiles[4] = "angry";
  $smiles[5] = "sad";
  $smiles[6] = "shocked";
  $smiles[7] = "cool";
  $smiles[8] = "smiley";
  $smiles[9] = "tongue";
  $smiles[10] = "embarassed";
  $smiles[11] = "lipsrsealed";
  $smiles[12] = "undecided";
  $smiles[13] = "kiss";
  $smiles[14] = "cry";
  $emb[0] = "::)";
  $emb[1] = ";)";
  $emb[2] = ":cheesy:";
  $emb[3] = ":grin:";
  $emb[4] = ":angry:";
  $emb[5] = ":(";
  $emb[6] = ":o";
  $emb[7] = ":cool:";
  $emb[8] = ":)";
  $emb[9] = ":tongue:";
  $emb[10] = ":embarassed:";
  $emb[11] = ":lipsrsealed:";
  $emb[12] = ":-/";
  $emb[13] = ":kiss:";
  $emb[14] = ":cry:";
  for ($i=0; $i<15; $i++) { 
    $str = str_replace($emb[$i], '<img src="./images/'.$smiles[$i].'.gif" width="15" height="15">', $str);
  }
  return $str;
}

function onLine() {
  $currentTime = time();
  // ≈сли пользователь 30 секунд не подает признаков 
  // жизни, считаем, что он покинул чат
  $offLine = time() - 30; 
  $file = file("online.txt");
  // ≈сли файл online.txt пустой, значит в чате никого нет
  if ( count( $file ) > 0 ) {
    $res = "";
    // количество пользователей on-line
    $onLine = 0;
    foreach ($file as $value) { 
      $line = explode("|", trim($value)); 
      if ($line[1] > $offLine) {
	    $res = $res.$value;
        $onLine = $onLine + 1;
      }	
    } 
    // перезаписываем файл
    if ( $fp = fopen("online.txt", "w") ) {
      if (flock($fp, LOCK_EX)) {
        fwrite($fp, $res);
        flock($fp, LOCK_UN);
      }
      fclose($fp);
    }
  }
  else {
    $onLine = 0;
  }
  return $onLine;
}
?>