<?php 
    require("config.php"); 
    session_start();
//    if (!isset($_SESSION)) { session_start(); }
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
//    if (empty($_SESSION['CCP']['user']['CharacterID'])&&empty($_SESSION['user'])) {
//        echo "<a href='#' id='ssoSelectButton'><img alt='EVE SSO Login Buttons Small White' src='https://images.contentful.com/idjq7aai9ylm/18BxKSXCymyqY4QKo8KwKe/c2bdded6118472dd587c8107f24104d7/EVE_SSO_Login_Buttons_Small_White.png?w=195&amp;h=30' data-pin-nopin='true'></a>";    
//    }

?> 
