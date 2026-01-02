<?php
$title = "Mon profil";
ob_start();
?>
<h2>Mon profil :</h2>


<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>