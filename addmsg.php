<?php 
  session_start(); 
  // ���� ������������ �� ����������� 
  if ( !isset($_SESSION["login"]) ) { 
    // ���� ����� ��� ����� ������ � ������ ���� ��������� 
    if ( isset($_POST["auth"]) ) { 
      $logpass = file( "passwords.txt" );  
      foreach ( $logpass as $value ) {        
        list( $login, $password ) = explode( "|", trim( $value ) );  
        if( ($_POST['login']==$login) && ($_POST['password']==$password) ) {  
          // ����������� ������ ������� 
          $_SESSION['login'] = $_POST['login'];
          header( "Location: addmsg.php" );
        }  
      }
    }	
    echo '<form name="authForm" method="post" action="addmsg.php">'; 
    echo '�����: <input type="text" name="login" value="" /><br/>'; 
    echo '������: <input type="password" name="password" value="" />'; 
    echo '<input type="submit" name="auth" value="����" />'; 
    echo '</form>'; 
    die(); 
  }
?>

<html>
<head>
<script type="text/javascript">
function AddSmile(text) {
  document.forms["addMsg"].elements["message"].value+=text;
  document.forms["addMsg"].elements["message"].focus();
}

function PutSmiles() {
  var pointer = "onmouseover=this.style.cursor='pointer'";
  var smiles = ["smiley", "wink", "cheesy", "grin", "angry", "sad", "shocked", "cool", 
  "rolleyes", "tongue", "embarassed", "lipsrsealed", "undecided", "kiss", "cry"];
  var emb = [":)", ";)", ":cheesy:", ":grin:", ":angry:", ":(", ":o", ":cool:", "::)", 
  ":tongue:", ":embarassed:", ":lipsrsealed:", ":-/", ":kiss:", ":cry:"];
  for (i = 0;i < 15; i++) {
    document.write("<img src='images/" + smiles[i] + ".gif' onclick='javascript: AddSmile(\" " + 
    emb[i] + "\");' " + pointer + " alt='" + smiles[i] + "' width='15' height='15' />&nbsp;");
  }
}

function CheckMsg(frm)
{
  if(frm.elements["message"].value == "") {
    alert("������� ���������");
    return false;
  }
  else
    return true;  
}
</script>
</head>
<body>
<form method="POST" name="addMsg" onSubmit="CheckMsg(this);">
<input type="text" name="person" maxlength="30" 
value="<?php echo $_SESSION["login"]; ?>" readonly="readonly" />
&nbsp;&nbsp;&nbsp;<script type='text/javascript'>PutSmiles()</script><br/>
<textarea name="message" rows="2" cols='60'></textarea><br/>
<input type="submit" value='��������'>
<img width="15" height="15" src="./images/refresh.gif" 
OnMouseOver='this.style.cursor="pointer";' alt="�������� ���!" 
OnClick="parent.showmsg.location.href = parent.showmsg.location.href;" 
style="position:absolute; top: 5px; right:5px" />

<?php
if ( isset($_POST['message']) and !empty($_POST['message']) ) {
  if ( filesize( "messages.txt" ) > 1 ) $file = file("messages.txt");
  // ��������� ����� ������
  $message = substr( $_POST["message"], 0, 250 );
  $message = htmlspecialchars( trim($message) );
  $message = preg_replace( "#\r?\n#", '<br/>', $message );
  $file[] = $_SESSION["login"]."�".date("d-m-y H:i:s")."�".$message."\n";
  // ������� ������ ������ (��������� ������ ������ ���������)
  $cnt = count( $file );
  if ( $cnt > 10 ) {
    for( $i = 0; $i < ($cnt-10); $i++ ) array_shift( $file );
  }     
  // �������������� ����
  if ( $fp = fopen("messages.txt", "w") ) {
    if (flock($fp, LOCK_EX)) {
      foreach ( $file as $msg ) fwrite($fp, $msg);
      flock($fp, LOCK_UN);
    }
    fclose($fp);
  }
  // ��������� ����, ��� �������� ���������� � ������������� on-line
  $file = file( "online.txt" );
  $cnt = count( $file );
  $id = session_id();
  for ( $i = 0; $i < $cnt; $i++ ) {
	$tmp = explode( "|", $file[$i] );
	// ���� �� ������ �� ���� ������������?
	if ( $tmp[0] == $id ) $on = $i;	  
  }
  if ( isset($on) ) {
	// ������ ��� ���� - ���� �������� �����
	$file[$on] = $id."|".time()."\n";
  } else {
	// ������ ��� �� ����, ������ ���������
	$file[] = $id."|".time()."\n";
  }
  if ( $fp = fopen("online.txt", "w") ) {
    if (flock($fp, LOCK_EX)) {
	  $c = count($file);
      for ( $i = 0; $i < $c; $i++) fwrite($fp, $file[$i]);
      flock($fp, LOCK_UN);
    }
    fclose($fp);
  }
}
?>

</body>
</html>