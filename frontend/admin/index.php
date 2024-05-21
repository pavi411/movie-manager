<?php
  $currentPage = 'Admin';
  $ROOT_PATH = '..';

  require_once($ROOT_PATH . '/config.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php require_once($ROOT_PATH . '/elements/header.php'); ?>
  </head>
  <body>
    <?php require_once($ROOT_PATH . '/elements/navbar.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/auth.php'); ?>

    <script type="text/javascript">
      function checkAdmin() {
        if(!checkLogin()) return false;
        let xhttp = new XMLHttpRequest();
        xhttp.open("POST", "<?php echo BACKEND . 'check_admin.php';?>", false);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('user=' + localStorage.getItem('JWT'));
        let response = JSON.parse(xhttp.responseText);
        if(response.Message == 'SUCCESS') {
          return response.isAdmin;
        }
        return false;
      }

      if(!checkAdmin()) window.location = '<?php echo FRONTEND; ?>';
    </script>

    <div class="container">
      <form id="search-form" class="my-2" oninput="search(this)" onsubmit="return false;">
        <div class="form-group">
          <div class="input-group border border-success rounded-pill" id="search-input">
            <input type="text" name="search_string" class="form-control border-0 rounded-left bg-transparent text-white-50" placeholder="Search" aria-label="Search Titles">
            <div class="input-group-append d-flex align-items-center bg-transparent px-2 rounded-right border-0">
              <i class="text-success fas fa-search fa-lg fa-fw"></i>
            </div>
          </div>
        </div>
        <div id="advanced-search" class="form-row">
          <div class="form-group col-12 col-md-4">
            <label for="genre-input">Genre:</label>
            <select id="genre-input" name="genre" class="form-control border-1 border-success bg-dark text-white-50">
              <option value="" selected>Choose...</option>
            </select>
          </div>
          <div class="form-group col-6 col-md-2">
            <label for="imdb-input">IMDb Rating:</label>
            <select id="imdb-input" name="imdb" class="form-control border-1 border-success bg-dark text-white-50">
              <option value="0" selected>Choose...</option>
            </select>
          </div>
          <div class="form-group col-6 col-md-2">
            <label for="mmdb-input">MMDb Rating:</label>
            <select id="mmdb-input" name="mmdb" class="form-control border-1 border-success bg-dark text-white-50">
              <option value="0" selected>Choose...</option>
            </select>
          </div>
          <div class="form-group col-6 col-md-2">
            <label for="sort-by-input">Sort By:</label>
            <select id="sort-by-input" name="sort_by" class="form-control border-1 border-success bg-dark text-white-50">
              <option value="" selected>Choose...</option>
            </select>
          </div>
          <div class="form-group col-6 col-md-2">
            <label for="sort-order-input">Sort Order:</label>
            <select id="sort-order-input" name="sort_order" class="form-control border-1 border-success bg-dark text-white-50">
            </select>
          </div>
        </div>
        <span class="d-flex justify-content-center">
          <button type="button" class="btn btn-dark" id="advanced-search-button" onclick="toggle_advanced_search()"><span class="">Advanced <i class="fas fa-chevron-down"></i></span><span class="d-none"><i class="fas fa-chevron-up"></i></span></button>
        </span>
      </form>
      <div id="page-container" class="d-none justify-content-between align-items-center my-2"><button type="button" id="prev-btn" class="btn btn-outline-success" onclick="changePage(-1)" disabled>Prev</button><span>Page: <h4 id="page-number" class="d-inline-block m-0">1</h4>/<span id="total-pages">1</span></span><button type="button" id="next-btn" class="btn btn-outline-success" onclick="changePage(1)" disabled>Next</button></div>
      <div id="item-container" class="row m-0"></div>
      <div id="page-container" class="d-none justify-content-between align-items-center my-2"><button type="button" id="prev-btn" class="btn btn-outline-success" onclick="changePage(-1)" disabled>Prev</button><span>Page: <h4 id="page-number" class="d-inline-block m-0">1</h4>/<span id="total-pages">1</span></span><button type="button" id="next-btn" class="btn btn-outline-success" onclick="changePage(1)" disabled>Next</button></div>
      <button type="button" class="btn btn-success" onclick="new_movie()">New Movie</button>
    </div>

    <script type="text/javascript">

      let genres = [
        'Action','Adventure','Animation','Biography','Comedy','Crime','Documentary','Drama','Family','Fantasy','Film-Noir','Game-Show','History','Horror','Music','Musical','Mystery','News','Reality-TV','Romance','Sci-Fi','Sport','Talk-Show','Thriller','War','Western'
      ];

      let sorts = {
        'ASC': 'Ascending',
        'DESC': 'Descending'
      }

      let sort_bys = {
        'title': 'Title',
        'runtime': 'Runtime',
        'year': 'Year'
      }

      let page = 1;
      let num_pages = 1;

      let genre_select = document.querySelector('#genre-input');
      for(let genre of genres) {
        let option = document.createElement('option');
        option.innerHTML = genre;
        option.setAttribute('value', genre);
        genre_select.appendChild(option)
      }

      let imdb_rating_select = document.querySelector('#imdb-input');
      let mmdb_rating_select = document.querySelector('#mmdb-input');
      for(let i = 1; i <= 10; i++) {
        let option = document.createElement('option');
        option.innerHTML = i + '+';
        option.setAttribute('value', i);
        imdb_rating_select.appendChild(option);
        mmdb_rating_select.appendChild(option.cloneNode(true));
      }

      let sort_by_select = document.querySelector('#sort-by-input');
      for(let [key, value] of Object.entries(sort_bys)) {
        let option = document.createElement('option');
        option.innerHTML = value;
        option.setAttribute('value', key);
        sort_by_select.appendChild(option)
      }

      let sort_order_select = document.querySelector('#sort-order-input');
      for(let [key, value] of Object.entries(sorts)) {
        let option = document.createElement('option');
        option.innerHTML = value;
        option.setAttribute('value', key);
        sort_order_select.appendChild(option)
      }

      let toggle_advanced_search = function() {
        document.querySelector('#advanced-search').classList.toggle('show');
        for(let e of document.querySelector('#advanced-search-button').childNodes) {
          e.classList.toggle('d-none');
        }
      }

      let process_search_response = function(json_response) {
        let itemList = document.querySelector('#item-container');
        itemList.innerHTML = "";
        item_list = {};
        console.log(json_response);
        let response = JSON.parse(json_response);
        if(response['Response'] == 'True') {
          for(let item of response['Search']) {
            itemList.appendChild(construct_item_thumb(item));
          }
          num_pages = Math.ceil(response['totalResults']/20);
        } else {
          itemList.innerHTML = response['Error'];
          num_pages = 0;
        }
        togglePageButtons();
      }

      let search = function(form, change_page = false) {
        let search_string = form.search_string.value;

        if(search_string == '') {
          hide_page_container();
          return;
        }

        let genre = form.genre.value;
        let imdb = form.imdb.value;
        let mmdb = form.mmdb.value;
        let sort_by = form.sort_by.value;
        let sort_order = form.sort_order.value;

        let search =  ('search=' + search_string) +
                      (genre != '' ? '&genre=' + genre : '') +
                      (imdb > 0 ? '&imdb=' + imdb : '') +
                      (mmdb > 0 ? '&mmdb=' + mmdb : '') +
                      (sort_by != '' ? '&sort_by=' + sort_by : '') +
                      (sort_order != '' ? '&sort_order=' + sort_order : '') +
                      (change_page ? '&page=' + page : '');

        if(change_page == false) {
          page = 1;
        }

        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            process_search_response(this.responseText);
          }
        };
        xhttp.open("GET", '<?php echo BACKEND; ?>search.php?' + search, true);
        xhttp.send();
      }

      function findGetParameter(parameterName) {
          var result = null,
              tmp = [];
          location.search
              .substr(1)
              .split("&")
              .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
              });
          return result;
      }

      let changePage = function(delta) {
        page = page + delta;
        if(page < 1) page = 1;
        if(page > num_pages) page = num_pages;
        search(document.querySelector('#search-form'), true);
      }

      let togglePageButtons = function() {
        for(let button of document.querySelectorAll('#prev-btn')) {
          button.disabled = true;
        }
        for(let button of document.querySelectorAll('#next-btn')) {
          button.disabled = true;
        }
        if(page > 1) {
          for(let button of document.querySelectorAll('#prev-btn')) {
            button.disabled = false;
          }
        }
        if(page < num_pages) {
          for(let button of document.querySelectorAll('#next-btn')) {
            button.disabled = false;
          }
        }
        for(let tf of document.querySelectorAll('#page-number')) {
          tf.innerHTML = page;
        }
        for(let tf of document.querySelectorAll('#total-pages')) {
          tf.innerHTML = num_pages;
        }
        if(num_pages > 1) {
          show_page_container();
        } else {
          hide_page_container();
        }
      }

      let hide_page_container = function() {
        for(let c of document.querySelectorAll('#page-container')) {
          c.classList.remove('d-flex');
          c.classList.add('d-none');
        }
      }

      let show_page_container = function() {
        for(let c of document.querySelectorAll('#page-container')) {
          c.classList.add('d-flex');
          c.classList.remove('d-none');
        }
      }

      let search_string = findGetParameter('search');

      if(search_string != null) {
        let search_form = document.querySelector('#search-form');
        search_form.search_string.value = search_string;
        search(search_form);
      }

      let new_movie = function() {
        reset_movie_details();
        document.querySelector('#edit-modal-button').click();
      }
    </script>

    <?php require_once($ROOT_PATH . '/elements/movie_details.php'); ?>
    <?php require_once($ROOT_PATH . '/admin/edit_movie_details.php'); ?>
    <?php require_once($ROOT_PATH . '/elements/footer.php'); ?>
  </body>
</html>
