<html>
<head>
<title>User</title>
</head>

<body>


<?php
	if(!@mysql_connect("localhost","jogos","jogos@aprendizado"))
	{
		echo "<h2>Error</h2>";
		die();
	}
	mysql_select_db("jogo_edupesca");

	
	if (@$_REQUEST['nivel'] != "") {
		$nivel=mysql_real_escape_string($_REQUEST['nivel']);
		$score=mysql_real_escape_string($_REQUEST['score']);
		//mysql_query("UPDATE user SET score=$score, nivelGamepesca=$nivel WHERE id=1;");
		
		$result = mysql_query("SELECT id FROM gamepesca WHERE ordem>(SELECT ordem FROM gamepesca WHERE id={$nivel}) ORDER BY ordem ASC LIMIT 1;") or die(mysql_error()); 
		
		if ($row = mysql_fetch_array($result)) {
			$novonivel = $row['id'];
			mysql_query("UPDATE user SET score=$score, nivelGamepesca=$novonivel WHERE id=1;");
		}
	}
	
	if (@$_REQUEST['action'] == "reset") {
		$result = mysql_query("SELECT id FROM gamepesca ORDER BY ordem ASC LIMIT 1;") or die(mysql_error()); 
		
		if ($row = mysql_fetch_array($result)) {
			$novonivel = $row['id'];
			mysql_query("UPDATE user SET nivelGamepesca=$novonivel WHERE id=1;");
		}
		
		header("location:painel.php");
	}
	
	if (@$_REQUEST['action'] == "deleteAll") {
		mysql_query("DELETE FROM gamepesca;") or die(mysql_error()); 
		header("location:painel.php");
	}
?>

</body>
</html>
