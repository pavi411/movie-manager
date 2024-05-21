<div id="notification-list"></div>

<script type="text/javascript">

  let addNotification = function(notification) {
    // Get notification container.
    let notifDiv = document.createElement('div');
    notifDiv.setAttribute('class', 'card notification bg-dark mb-2');
    notifDiv.setAttribute('id', 'notif-' + notification['id']);

    // Header
    let notifHeader = document.createElement('div');
    notifHeader.setAttribute('class', 'card-header border-success d-flex align-items-center justify-content-between');
    notifHeader.innerHTML = '<strong>Notification</strong><button type="button" class="btn p-0 text-light" onclick="removeNotification(' + notification['id'] + ')"><i class="fas fa-times fa-lg"></i></button>';
    notifDiv.appendChild(notifHeader);

    // Body
    let notifBody = document.createElement('div');
    notifBody.setAttribute('class', 'card-body');

    notifBodyText = document.createElement('p');
    notifBodyText.setAttribute('class', 'card-text');
    notifBodyText.innerHTML = notification['text'];
    notifBody.appendChild(notifBodyText);

    notifButton = document.createElement('button');
    notifButton.setAttribute('type', 'button');
    notifButton.setAttribute('class', 'btn btn-primary');
    notifButton.onclick = notification['button'].onclick;
    notifButton.innerHTML = notification['button'].text;
    notifBody.appendChild(notifButton);

    notifDiv.appendChild(notifBody);

    let notifList = document.querySelector('#notification-list');
    if((window.innerWidth < 576 || notifList.offsetHeight > window.innerHeight) && notifList.childElementCount > 0) {
      notifList.removeChild(notifList.childNodes[0]);
    }
    document.querySelector('#notification-list').appendChild(notifDiv);
  }

  let removeNotification = function(id) {
    document.querySelector('#notification-list').removeChild(document.querySelector('#notif-' + id));
  }

  let generateNotification = function(notification_data) {
    if(notification_data['type'] == 2) {
      notification_data['text'] = '<strong>' + notification_data['from'] + '</strong> voted on your review.';
      notification_data['button'] = {
        'onclick': function() {
          show_movie_by_id(notification_data['movie']);
        },
        'text': 'View'
      };
    }
    addNotification(notification_data);
    addNotificationToDropdown(notification_data);
  }

  let addNotificationToDropdown = function(notification) {
    let notifDropdown = document.querySelector('#notifDropdown');
    let notif = document.createElement('button');
    notif.setAttribute('type', 'button');
    notif.setAttribute('class', 'dropdown-item');
    if(notification['type'] == 2) {
      notif.innerHTML = '<strong>' + notification['from'] + '</strong> voted on your review.';
      notif.onclick = function() {
        show_movie_by_id(notification['movie']);
      }
    }
    if(notifDropdown.childElementCount >  6) {
      notifDropdown.removeChild(notifDropdown.childNodes[0]);
    }
    notifDropdown.appendChild(notif);
  }

  let getNotifications = function(num = 10, page = 1) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
        let response = JSON.parse(this.responseText);
        if(response['Message'] == 'SUCCESS' && response['unreadNotifications'] > 0) {
          document.querySelector('#notification-button').classList.remove('far');
          document.querySelector('#notification-button').classList.add('fas');
          document.querySelector('#notif-number').innerHTML = response['unreadNotifications'];
          document.querySelector('#notif-number').classList.remove('d-none');
          document.querySelector('#notifDropdownButton').click();
        }
        for(let notification of response['Notifications']) {
          if(document.title == 'Notifications') {
            addNotificationToList(notification);
          } else {
            addNotificationToDropdown(notification);
          }
        }
      }
    };
    xhttp.open("POST", "<?php echo BACKEND . 'get_notifications.php';?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('user=' + localStorage.getItem('JWT') + '&num=' + num + '&page=' + page);
  }

  let socket = null;

  if(checkLogin()) {
    socket = new WebSocket('<?php echo NOTIFICATION_SERVER; ?>');

    socket.onopen = function(e) {
      let loginData = {
        'type': 'CONNECT',
        'user': localStorage.getItem('JWT')
      };
      socket.send(JSON.stringify(loginData));
    };

    socket.onmessage = function(event) {
      console.log(event.data);
      let data = JSON.parse(decodeURIComponent(event.data));
      if(data.Message != 'CONNECTED' || data.Message != 'SENT') {
          generateNotification(data);
      }
    };

    socket.onclose = function(event) {
      if (event.wasClean) {
        console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`);
      } else {
        // e.g. server process killed or network down
        // event.code is usually 1006 in this case
        console.log('[close] Connection died');
      }
    };

    socket.onerror = function(error) {
      console.log(`[error] ${error.message}`);
    };

    getNotifications();
  }

</script>
