<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-success">
  <a class="navbar-brand" href="">MMDb</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <?php
        $nav_links = array(
          'Home' => './index.php',
          'Browse' => './browse.php'
        );
        foreach ($nav_links as $name => $url) {
          echo '<li class="nav-item'. (($currentPage == $name) ? ' active' : '') . '">'
              .'<a class="nav-link" href="'. $url .'">' . $name . '</a></li>';
        }
      ?>
    </ul>
    <ul class="navbar-nav">
      <div class="btn-group d-none" id="logout-buttons">
        <button type="button" class="btn btn-dark" id="notifDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span id="notif-number" class="badge badge-light d-none">4</span><i id="notification-button" class="far fa-bell fa-lg"></i>
        </button>
        <div class="dropdown">
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notifDropdownButton">
            <div id="notifDropdown"></div>
          </div>
        </div>
        <button type="button" id="logout-button" class="btn btn-dark" onclick="logout()">Log Out</button>
      </div>
      <div class="btn-group" id="login-buttons">
        <button type="button" id="login-button" class="btn btn-dark" data-toggle="modal" data-target="#loginModal">Log In</button>
        <button type="button" id="register-button" class="btn btn-dark" data-toggle="modal" data-target="#registerModal">Register</button>
      </div>
    </ul>
  </div>
</nav>
