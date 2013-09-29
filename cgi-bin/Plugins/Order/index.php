<html>
	<head></head>
	<body>
		<?php
			require_once("Bestellablauf.php");
			 
			BestellAblauf::GetInst()->aktuellerBestellSchritt(); 
		?>
	</body>
</html>