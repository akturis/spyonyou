<?php 
    require("config.php"); 
    if (!isset($_SESSION)) { session_start(); }
    if (!empty($_SESSION['user'])) {

            echo '      
                <div class="nav-collapse collapse">
                <ul class="nav pull-right">
                <button type="submit" id="logout" class="btn btn-block">'.$_SESSION['user'].'</button>
                 </ul>
                </div>';    
    }
    elseif (!empty($_SESSION['CCP']['user']['CharacterID'])) {

            echo '      
                <div class="nav-collapse collapse">
                <ul class="nav pull-right">
                <button type="submit" id="logout" class="btn btn-block">'.$_SESSION['CCP']['user']['CharacterName'].'</button>
                 </ul>
                </div>';    
    }
    else {
        echo "      
        <div class='nav-collapse collapse'>
        <ul class='nav pull-right'>
        <li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' id='logging' href='#'>Login <span class='glyphicon glyphicon-log-in'></span></a>
          <div class='dropdown-menu'>
            <form id='formLogin' class='form container-fluid'>
              <div class='form-group'>
                <label for='usr'>Name:</label>
                <input type='text' class='form-control' id='usr'>
              </div>
              <div class='form-group'>
                <label for='pwd'>Password:</label>
                <input type='password' class='form-control' id='pwd'>
              </div>
              <button type='submit' id='submit' class='btn btn-block'>Login</button>
            </form>
          </div>
        </li>
        </ul>
      </div>";    
    }
?> 