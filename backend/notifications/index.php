<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

    <script type="text/javascript">
      let socket = new WebSocket("ws://localhost:9000/");
      socket.onopen = function(e) {
        console.log("[open] Connection established, send -> server");
      };
      socket.onmessage = function(event) {
        console.log(event.data);
      };
    </script>
  </body>
</html>
