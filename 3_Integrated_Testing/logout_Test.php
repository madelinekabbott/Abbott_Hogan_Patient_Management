

<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class logout_Test extends TestCase
{
   

<?php
session_start();
session_unset();  
session_destroy(); 

header("Location: login.php");
exit();
?>
}