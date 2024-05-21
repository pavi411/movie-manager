<!-- Button trigger modal -->
<button type="button" class="d-none" id="edit-modal-button" data-toggle="modal" data-target="#editModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editDetailsModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header border-success">
        <h5 class="modal-title" id="editDetailsModal">Edit Movie Details</h5>
        <button type="button" id="edit-details-modal-close" class="close text-light" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="movie-edit-form" onsubmit="return false;">
          <div class="form-group">
            <label for="edit-imdb-id">IMDb ID</label>
            <input type="text" class="form-control" id="edit-imdb-id" name="imdb_id" placeholder="IMDb ID">
          </div>
          <div class="form-group">
            <label for="edit-title">Title</label>
            <input type="text" class="form-control" id="edit-title" name="title" placeholder="Title">
          </div>
          <div class="form-group">
            <label for="edit-synopsis">Synopsis</label>
            <textarea name="synopsis" class="form-control" id="edit-synopsis" rows="4" placeholder="Synopsis"></textarea>
          </div>
          <div class="form-group" id="edit-genres">
            Genres<br>
          </div>
          <div class="form-group">
            <label for="edit-language">Language</label>
            <input type="text" class="form-control" id="edit-language" name="language" placeholder="Language">
          </div>
          <div class="form-group">
            <label for="edit-mpa">MPA Rating</label>
            <input type="text" class="form-control" id="edit-mpa" name="mpa_rating" placeholder="MPA Rating">
          </div>
          <div class="form-group">
            <label for="edit-imdb-rating">IMDb Rating</label>
            <input type="text" class="form-control" id="edit-imdb-rating" name="imdb_rating" placeholder="IMDb Rating">
          </div>
          <div class="form-group">
            <label for="edit-runtime">Runtime</label>
            <input type="text" class="form-control" id="edit-runtime" name="runtime" placeholder="Runtime">
          </div>
          <div class="form-group">
            <label for="edit-year">Year</label>
            <input type="text" class="form-control" id="edit-year" name="year" placeholder="Year">
          </div>
          <div class="form-group">
            <label for="edit-poster">Poster URL</label>
            <input type="text" class="form-control" id="edit-poster" name="poster" placeholder="URL">
          </div>
          <div class="form-group">
            <label for="edit-yt">Youtube Trailer Code</label>
            <input type="text" class="form-control" id="edit-yt" name="yt_trailer_code" placeholder="Youtube Code">
          </div>
          <h5>Torrents</h5>
          <div id="edit-torrents"></div>
          <button type="button" class="btn btn-info" onclick="addTorrent()">Add Torrent</button>
          <br>
          <br>
          <button type="button" class="btn btn-success" onclick="submitEdit()">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  let torrent_ids = [];

  function addTorrentInput(id, torrent = null) {
    let container = document.createElement('div');
    container.setAttribute('class', 'form-row mb-2');
    container.setAttribute('id', 'torrent-container-' + id);

    let urlContainer = document.createElement('div');
    urlContainer.setAttribute('class', 'col-5');

    let urlInput = document.createElement('input');
    urlInput.setAttribute('type', 'text');
    urlInput.setAttribute('class', 'form-control');
    urlInput.setAttribute('placeholder', 'URL');
    urlInput.setAttribute('id', 'url_' + id);
    urlInput.value = (torrent == null) ? '' : torrent.url;
    urlContainer.appendChild(urlInput);

    container.appendChild(urlContainer);

    let qualityContainer = document.createElement('div');
    qualityContainer.setAttribute('class', 'col-2');

    let qualityInput = document.createElement('input');
    qualityInput.setAttribute('type', 'text');
    qualityInput.setAttribute('class', 'form-control');
    qualityInput.setAttribute('placeholder', 'Quality');
    qualityInput.setAttribute('id', 'quality_' + id);
    qualityInput.value = torrent !== null ? torrent.quality : '';
    qualityContainer.appendChild(qualityInput);

    container.appendChild(qualityContainer);

    let typeContainer = document.createElement('div');
    typeContainer.setAttribute('class', 'col-2');

    let typeInput = document.createElement('input');
    typeInput.setAttribute('type', 'text');
    typeInput.setAttribute('class', 'form-control');
    typeInput.setAttribute('placeholder', 'Type');
    typeInput.setAttribute('id', 'type_' + id);
    typeInput.value = torrent !== null ? torrent.type : '';
    typeContainer.appendChild(typeInput);

    container.appendChild(typeContainer);

    let sizeContainer = document.createElement('div');
    sizeContainer.setAttribute('class', 'col-2');

    let sizeInput = document.createElement('input');
    sizeInput.setAttribute('type', 'text');
    sizeInput.setAttribute('class', 'form-control');
    sizeInput.setAttribute('placeholder', 'Size');
    sizeInput.setAttribute('id', 'size_' + id);
    sizeInput.value = torrent !== null ? torrent.size : '';
    sizeContainer.appendChild(sizeInput);

    container.appendChild(sizeContainer);

    let removeButton = document.createElement('button');
    removeButton.setAttribute('type', 'button');
    removeButton.setAttribute('class', 'col btn btn-danger');
    removeButton.onclick = function() {
      for(let i in torrent_ids) {
        if(torrent_ids[i] == id) {
          torrent_ids.splice(i, 1);
        }
        document.querySelector('#torrent-container-' + id).remove();
      }
    }
    removeButton.innerHTML = 'X';

    container.appendChild(removeButton);

    return container;
  }

  function reset_movie_details() {
    document.querySelector('#edit-imdb-id').value = '';
    document.querySelector('#edit-imdb-id').removeAttribute('disabled');
    document.querySelector('#edit-title').value = '';
    document.querySelector('#edit-synopsis').value = '';
    for(let genre of genres) {
      document.querySelector('#genre-' + genre).checked = false;
    }
    document.querySelector('#edit-language').value = '';
    document.querySelector('#edit-mpa').value = '';
    document.querySelector('#edit-imdb-rating').value = '';
    document.querySelector('#edit-runtime').value = '';
    document.querySelector('#edit-year').value = '';
    document.querySelector('#edit-poster').value = '';
    document.querySelector('#edit-yt').value = '';
    document.querySelector('#edit-torrents').innerHTML = '';
    torrent_ids = [];
  }

  function edit_movie_details(item) {
    document.querySelector('#edit-imdb-id').value = item.imdb_code || '';
    document.querySelector('#edit-imdb-id').setAttribute('disabled', 'true');
    document.querySelector('#edit-title').value = item.title || '';
    document.querySelector('#edit-synopsis').value = item.synopsis || '';
    for(let genre of item.genres.split(',')) {
      document.querySelector('#genre-' + genre).checked = true;
    }
    document.querySelector('#edit-language').value = item.language || '';
    document.querySelector('#edit-mpa').value = item.mpa_rating || '';
    document.querySelector('#edit-imdb-rating').value = item.imdb_rating || '';
    document.querySelector('#edit-runtime').value = item.runtime || '';
    document.querySelector('#edit-year').value = item.year || '';
    document.querySelector('#edit-poster').value = item.poster || '';
    document.querySelector('#edit-yt').value = item.yt_trailer_code || '';
    for(let torrent of item.torrents) {
      let tid = Math.random()*Math.pow(10, 20);
      document.querySelector('#edit-torrents').appendChild(addTorrentInput(tid, torrent));
      torrent_ids.push(tid);
    }
  }

  function addTorrent() {
    let tid = Math.random()*Math.pow(10, 20);
    document.querySelector('#edit-torrents').appendChild(addTorrentInput(tid));
    torrent_ids.push(tid);
  }

  function addGenreRadios() {
    let genre_select = document.querySelector('#edit-genres');
    for(let genre of genres) {
      let container = document.createElement('div');
      container.setAttribute('class', 'form-check form-check-inline');

      let checkbox = document.createElement('input');
      checkbox.setAttribute('type', 'checkbox');
      checkbox.setAttribute('class', 'form-check-input');
      checkbox.setAttribute('id', 'genre-' + genre);
      checkbox.setAttribute('name', 'genre-' + genre);

      let label = document.createElement('label');
      label.setAttribute('class', 'form-check-label');
      label.setAttribute('for', 'genre-' + genre);
      label.innerHTML = genre;

      container.appendChild(checkbox);
      container.appendChild(label);
      genre_select.appendChild(container);
    }
  }

  function submitEdit() {
    let e = document.querySelector('#movie-edit-form');
    let movie_data = {
      'imdb_code': e.imdb_id.value,
      'title': e.title.value,
      'synopsis': e.synopsis.value,
      'language': e.language.value,
      'mpa_rating': e.mpa_rating.value,
      'imdb_rating': e.imdb_rating.value,
      'runtime': e.runtime.value,
      'year': e.year.value,
      'poster': e.poster.value,
      'yt_trailer_code': e.yt_trailer_code.value
    }
    let movie_genres = [];
    for(let genre of genres) {
      if(document.querySelector('#genre-' + genre).checked) {
        movie_genres.push(genre);
      }
    }
    movie_data['genres'] = movie_genres.join(',');
    movie_data['torrents'] = [];

    for(let tid of torrent_ids) {
      console.log(tid);
      movie_data['torrents'].push({
        'url': document.querySelector('#url_' + tid).value,
        'quality': document.querySelector('#quality_' + tid).value,
        'type': document.querySelector('#type_' + tid).value,
        'size': document.querySelector('#size_' + tid).value
      });
    }

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
      }
    };
    xhttp.open("POST", "<?php echo BACKEND . 'edit_movie.php';?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('user=' + localStorage.getItem('JWT') + '&movie_data=' + JSON.stringify(movie_data) + '&type=add');
  }
  addGenreRadios();
</script>
