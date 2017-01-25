<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
		
		$servername = "db.veldin.com"; 
		$username = "md253219db370063"; 
		$password = "NiFQYCvz"; 
		$dbname = "md253219db370063";  

		$DBConnect = mysqli_connect($servername,$username,$password);

        if ($DBConnect == FALSE)
        {
            echo "<p>Unable to connect to database.</p>"
            . "<p>Erro code" . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>";
        } else
        {
            $selectdb = mysqli_select_db($DBConnect, $dbname);
        }
        if ($selectdb == FALSE)
        {
            echo "<p>Unable to select the database.</p>"
            . "<p>Erro code" . mysqli_errno($selectdb) . ": " . mysqli_error($selectdb) . "</p>";
        }

        $SQLstring = "SELECT user.firstname, user.lastname, chat.message FROM chat, user WHERE chat.userid = user.id ORDER BY chat.timestamp DESC LIMIT 10";
        $QueryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($QueryResult) == 0)
        {
            echo "<p>There are no messages yet!</p>";
        } else
        {
            echo "<p>Chat:</p>";
            while ($Row = mysqli_fetch_assoc($QueryResult))
            {
                echo "{$Row['firstname']} {$Row['lastname']}: {$Row['message']}<br>";
            }
        } mysqli_free_result($QueryResult);
        ?>
    </body>
</html>