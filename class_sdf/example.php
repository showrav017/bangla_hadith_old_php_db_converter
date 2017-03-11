<?php

/**
 * @author Rainer Mohr
 * @copyright 2011
 * Example for clss_sdf
 */

include("class_sdf.php");

$sdf = new sdf("testdatabase.sdf", "password@1"); 

$sdf->execute("SELECT id, text FROM test");

while( !$sdf->eof() )
{
    echo "The row with the id ".$sdf->fieldvalue('id')." has the value: \"".$sdf->fieldvalue('text')."\"<br>";
    $sdf->movenext();
}

$sdf->close();

?>