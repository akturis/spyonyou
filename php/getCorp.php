<?php
    require_once("config.php");
    echo("            <li class='dropdown'>");
    echo("<a id='corp'  role='button' class='dropdown-toggle' data-toggle='dropdown'>FSP-T <b class='caret'></b></a>
                <ul class='dropdown-menu' role='menu' aria-labelledby='Corporation'>");
    foreach($setting['corpID'] as $name => $corpID){
        $name_id = strtolower($name);
        echo("          <li><a tabindex='-1' href='#' id='{$name_id}'>{$name}</a></li>");
    }
    echo("            </ul>
            </li>
    ");
?>
