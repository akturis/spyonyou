<?php
require_once("config.php");

if (!empty($_SERVER['PHP_AUTH_USER'])) {
    echo "<form action=''>
            Interval
            <select onChange='setOption('days', parseInt(this.value, 30))'>
                <option value='30'>30 days</option>
                <option value='0'>All days</option>
            </select>
          </form>";
}
?>

