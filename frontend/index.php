<?php
  $currentPage = 'Home';
  $ROOT_PATH = '.';

  require_once($ROOT_PATH . '/config.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once($ROOT_PATH . '/elements/header.php'); ?>

    <link rel="stylesheet" href="<?php echo $ROOT_PATH; ?>/css/scrollable_div.css">
  </head>
  <body>
    <?php require_once($ROOT_PATH . '/elements/navbar.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/auth.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/notifications.php'); ?>

    <div class="container">
      <form id="search-form" method="get" action="browse.php" class="mt-2">
        <div class="form-group">
          <div class="input-group border border-success rounded-pill" id="search-input">
            <input type="text" name="search" class="form-control border-0 rounded-left bg-transparent text-white-50" placeholder="Search" aria-label="Search Titles">
            <div class="input-group-append d-flex align-items-center bg-transparent px-2 rounded-right border-0">
              <button type="submit" class="btn p-0 text-success fas fa-search fa-lg fa-fw"></button>
            </div>
          </div>
        </div>
      </form>
      <div>
        <h3>Latest Movies</h3>
        <div class="scrollable-div mb-2" id='latest'>
          <button class="left-scrollbar"><</button>
          <div id="latest-list" class="scroll-items">
          </div>
          <button class="right-scrollbar">></button>
        </div>
      </div>
      <div id="recommendations" class="d-none">
        <h3>Recommendations</h3>
        <div class="scrollable-div mb-2" id='recommendations-0'>
          <button class="left-scrollbar"><</button>
          <div id="recommendation-list-0" class="scroll-items">
          </div>
          <button class="right-scrollbar">></button>
        </div>
        <h3>Similar to movies you liked</h3>
        <div class="scrollable-div" id='recommendations-1'>
          <button class="left-scrollbar"><</button>
          <div id="recommendation-list-1" class="scroll-items">
          </div>
          <button class="right-scrollbar">></button>
        </div>
      </div>
    </div>

    <script type="text/javascript">
      let process_recommendation = function(json_response) {
        let response = JSON.parse(json_response);
        if(response['Message'] != 'SUCCESS') {
            return;
        }
        document.querySelector('#recommendations').classList.remove('d-none');
        for(let i = 0; i < 2; i++) {
          let itemList = document.querySelector('#recommendation-list-' + i);
          for(let item of response['Recommendations'][i]) {
            item_thumb = construct_item_thumb(item);
            item_thumb.classList.add('scroll-item');
            itemList.appendChild(item_thumb);
          }
          let scrollable = new ScrollableDiv(document.querySelector('#recommendations-' + i));
        }
      }

      let get_recommendations = function() {
        if(checkLogin() == false) return;

        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            process_recommendation(this.responseText);
          }
        };
        xhttp.open("POST", "<?php echo BACKEND . 'recommendations.php';?>", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('JWT=' + localStorage.getItem('JWT'));
      }

      let process_latest_response = function(json_response) {
        let itemList = document.querySelector('#latest-list');
        let response = JSON.parse(json_response);
        if(response['Response'] == 'True') {
          for(let item of response['Search']) {
            item_thumb = construct_item_thumb(item);
            item_thumb.classList.add('scroll-item');
            itemList.appendChild(item_thumb);
          }
          let scrollable = new ScrollableDiv(document.querySelector('#latest'));
        }
      }

      let get_latest = function() {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            process_latest_response(this.responseText);
          }
        };
        xhttp.open("GET", "<?php echo BACKEND . 'search.php?sort_by=id&sort_order=DESC';?>", true);
        xhttp.send();
      }

      get_recommendations();
      get_latest();
    </script>

    <script src="<?php echo $ROOT_PATH; ?>/helpers/scrollable_div.js" charset="utf-8"></script>

    <script type="text/javascript">
    </script>

    <?php require_once($ROOT_PATH . '/elements/movie_details.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/footer.php'); ?>
  </body>
</html>
