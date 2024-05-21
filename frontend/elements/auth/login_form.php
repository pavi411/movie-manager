<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header border-success">
        <h5 class="modal-title" id="ModalCenterTitle">Log In</h5>
        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="login-message" class="d-none"></p>
        <form>
          <div class="form-group">
            <label for="loginUsername">Username</label>
            <input type="text" class="form-control" id="loginUsername" placeholder="Username" autocomplete="username" required>
          </div>
          <div class="form-group">
            <label for="loginPassword">Password</label>
            <input type="password" class="form-control" id="loginPassword" placeholder="Password" autocomplete="current-password" required>
          </div>
          <div class="d-flex flex-row justify-content-between align-items-center">
            <button type="button" class="btn btn-success" onclick="login()">Log In</button>
            <button type="button" class="btn btn-link" onclick="open_register_modal()">Dont have an account?</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function show_login_message() {
    document.querySelector('#login-message').classList.remove('d-none');
  }
  function login() {
    if(localStorage.getItem('JWT')) {
      document.querySelector('#login-message').innerHTML = 'Already Logged In.';
      show_login_message();
    } else {
      let login_data = {
        'username': document.querySelector('#loginUsername').value,
        'password': document.querySelector('#loginPassword').value
      }
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          let response = JSON.parse(this.responseText);
          if(response.message == 'SUCCESS') {
            document.querySelector('#login-message').innerHTML = 'Login Successful';
            localStorage.setItem('JWT', response.jwt);
            localStorage.setItem('username', response.username);
            location.reload();
          } else {
            document.querySelector('#login-message').innerHTML = 'Invalid Credentials';
            show_login_message();
          }
        }
      };
      xhttp.open("POST", "<?php echo BACKEND . 'login.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('login=' + JSON.stringify(login_data));
    }
  }
</script>
