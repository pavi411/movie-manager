<?php
  require_once('auth/login_form.php');
  require_once('auth/register_form.php');
  require_once('auth/logout_button.php');
?>
<script type="text/javascript">
  function open_register_modal() {
    closeAllModals();
    document.querySelector('#register-button').click();
  }
  function open_login_modal() {
    closeAllModals();
    document.querySelector('#login-button').click();
  }
  function toggle_auth_buttons() {
    buttons = ['#logout-buttons', '#login-buttons'];
    for(let button of buttons) {
      for(let btn of document.querySelectorAll(button)) {
        btn.classList.toggle('d-none');
      }
    }
  }
  function closeAllModals() {
    // Get modal close buttons.
    let close_buttons = document.querySelectorAll('.close');
    for(let cb of close_buttons) {
      cb.click();
    }
  }
  function checkLogin() {
    return localStorage.getItem('JWT') != null
  }

  if(checkLogin()) {
    toggle_auth_buttons();
  }
</script>
