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

	
	if(@$_REQUEST['nivel']!="")
	{
	    $nivel=mysql_real_escape_string($_REQUEST['nivel']);
		$score=mysql_real_escape_string($_REQUEST['score']);

		mysql_query("UPDATE user SET score=$score, nivelGamepesca=$nivel WHERE id=1;");
	}
    
	$result=mysql_query("SELECT score, nivelGamepesca FROM user ORDER BY id;");
	
	if($result === FALSE) { 
    die(mysql_error()); 
    }
	echo "<table>";
	while( $row=mysql_fetch_array($result) )
	{
		echo "<tr>";
		echo "<td class=tabval><b>".$row['nivelGamepesca']." |</b></td>";
		echo "<td class=tabval><b>".$row['score']."</b></td>";
		echo "</tr>";
	}
    echo "</table>";

?>

</body>
</html>
