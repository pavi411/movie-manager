<script type="text/javascript">
      function logout() {
        if(localStorage.getItem('JWT') != null) {
          localStorage.removeItem('JWT');
          location.reload();
        }
      }
</script>
