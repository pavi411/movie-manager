<!-- Button trigger modal -->
<button type="button" id="modal-button" class="d-none" data-toggle="modal" data-target="#detailsModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header border-success">
        <h5 class="modal-title" id="detailsModal">Movie Details</h5>
        <button type="button" id="details-modal-close" class="close text-light" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row d-flex">
            <div class="col-12 col-lg-4 col-xl-3 order-lg-1">
              <img src="https://img.yts.lt/assets/images/movies/9th_company_2005/medium-cover.jpg" class="img-thumbnail" alt="Poster Unavailable." id="modal-poster">
            </div>
            <div class="col-12 col-lg-8 col-xl-6 d-flex flex-column order-lg-2">
              <span class="mb-4">
                <h1 class="d-inline" id="modal-details-title">9th Company</h1>
              </span>
              <h5 id="modal-details-year">2005</h5>
              <h5 class="mb-4" id="modal-details-genres"></h5>
              <span class="mb-4 d-flex flex-column align-items-center flex-lg-row align-items-center">
                <span class="d-flex flex-row flex-lg-column justify-content-around col-12 col-lg-3 pr-lg-2 px-0">
                  <span class="col-6 col-sm-4 col-lg-12 p-0 mb-2 d-inline-flex d-lg-flex align-items-center justify-content-between">
                    <img src="assets/imdb_logo.png" style="height:24px;" alt="">
                    <span><span id="modal-details-imdb-rating" class="mr-2">7.1</span><i class="fas fa-star"></i></span>
                  </span>
                  <span class="col-6 col-sm-4 col-lg-12 p-0 mb-2 mb-lg-0 d-inline-flex d-lg-flex align-items-center justify-content-between">
                    <span>MMDB</span>
                    <span><span id="modal-details-mmdb-rating" class="mr-2">7.1</span><i class="fas fa-star"></i></span>
                  </span>
                </span>
                <span class="d-flex flex-column align-items-center flex-sm-row">
                  <span id="rating-toggler" class="row m-0 text-center" onclick="toggleRating()">
                    <i class="far fa-star fa-3x p-2" id="rating-star"></i>
                    <span class="d-flex flex-column justify-content-center" id="rating-star-text"><span>Rate</span><span>This</span></span>
                  </span>
                  <span class="d-none ml-2 p-2 align-items-center border border-success rounded" id="rating-toggle">
                    <span id="rating-container"></span>
                  </span>
                </span>
              </span>
              <span class="mb-4 d-flex flex-column flex-sm-row align-items-center">
                <h5 class="d-inline my-auto mr-2"><em>Download in:</em></h5>
                <div id="modal-details-download-links" class="btn-group">
                  <a class="btn btn-outline-success btn-sm">720p Bluray</a>
                  <a class="btn btn-outline-success btn-sm">1080p Bluray</a>
                  <a class="btn btn-outline-success btn-sm">3D Bluray</a>
                </div>
              </span>
              <span>
                <h5>Synopsis: </h5>
                <p id="modal-details-synopsis" class="text-justify">The film is based on a true story of the 9th company during the Soviet invasion in Afghanistan in the 1980's. Young Soviet Army recruits are sent from a boot camp into the middle of the war in Afghanistan. The action is not like a boot camp at all. It is very bloody and dirty. The 9th company is defending the hill 3234. They are hopelessly calling for help.</p>                  </span>
            </div>
            <div class="col-12 mb-4 order-lg-0">
              <h5>Trailer</h5>
              <div id="modal-video" class="embed-responsive embed-responsive-16by9">
                <iframe id="modal-details-trailer" class="embed-responsive-item" src="https://www.youtube.com/embed/c991IDTFr0g?rel=0" allowfullscreen></iframe>
              </div>
            </div>
            <div class="col-12 col-xl-3 order-lg-3">
              <h6>Similar Movies:</h6>
              <div id="similar-movies-list" class="row">
                <a class="col-6 col-sm-3 col-xl-6">
                  <img src="" alt="">
                </a>
              </div>
            </div>
            <div class="col-12 order-lg-4">
              <ul class="list-group list-group-flush border-0 rounded" id="modal-review-list">
              </ul>
              <button type="button" class="btn btn-info d-none" onclick="getReviews()" id="modal-review-load">Load More</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  let current_movie = 0;
  let current_movie_rating = 0;
  let review_page = 1;
  let review_count = 5;
  let upvotes = {};

  function get_user_rating() {
    return new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          let response = JSON.parse(this.responseText);
          if(response.Message == 'SUCCESS') {
            resolve(response.Rating);
          } else {
            resolve(0);
          }
        }
      };
      xhttp.open("POST", "<?php echo BACKEND . 'get_rating.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('type=get&user=' + localStorage.getItem('JWT') + '&movie=' + current_movie);
    });
  }

  function get_movie_rating() {
    return new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          let response = JSON.parse(this.responseText);
          if(response.Message == 'SUCCESS') {
            resolve(response.Rating);
          } else {
            resolve(0);
          }
        }
      };
      xhttp.open("POST", "<?php echo BACKEND . 'get_rating.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('type=get&movie=' + current_movie);
    });
  }

  function rate_movie() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        let response = JSON.parse(this.responseText);
        if(response.Message != 'SUCCESS') {
          console.log(response);
        }
      }
    };
    xhttp.open("POST", "<?php echo BACKEND . 'get_rating.php';?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('type=set&user=' + localStorage.getItem('JWT') + '&movie=' + current_movie + '&rating=' + current_movie_rating);
  }

  let getReviews = function() {
    return new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = async function() {
        if (this.readyState == 4 && this.status == 200) {
          await process_reviews(this.responseText);
          resolve();
        }
      };
      xhttp.open("POST", "<?php echo BACKEND . 'get_review.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('type=get&movie=' + current_movie + '&page=' + review_page);
    });
  }

  let getUserReview = function() {
    return new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = async function() {
        if (this.readyState == 4 && this.status == 200) {
          await process_user_review(this.responseText);
          resolve();
        }
      };
      xhttp.open("POST", "<?php echo BACKEND . 'get_review.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('type=get&movie=' + current_movie + '&user=' + localStorage.getItem('JWT'));
    });
  }

  let getUserVote = function(review_id) {
    return new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          let response = JSON.parse(this.responseText);
          if(response.Message == 'SUCCESS') {
            resolve(response.Vote);
          } else {
            resolve(0);
          }
        }
      };
      xhttp.open("POST", "<?php echo BACKEND . 'get_upvotes.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send('type=get&review=' + review_id + '&user=' + localStorage.getItem('JWT'));
    });
  }

  let process_reviews = async function(json_response) {
    let response = JSON.parse(json_response);
    let review_list = document.querySelector('#modal-review-list');
    if(response['Message'] == 'SUCCESS') {
      for(let review of response['Reviews']) {
        if(upvotes[review.id] == undefined) {
          review_list.appendChild(await construct_review(review));
        }
      }
      if(++review_page * review_count <= response['totalReviews']) {
        document.querySelector('#modal-review-load').classList.remove('d-none');
      } else {
        document.querySelector('#modal-review-load').classList.add('d-none');
      }
    }
  }

  let process_user_review = async function(json_response) {
    let response = JSON.parse(json_response);
    let review_list = document.querySelector('#modal-review-list');
    if(response['Message'] == 'SUCCESS') {
      review_list.innerHTML = '<h5>Your Review</h5>';

      let yourReview = document.createElement('div');
      yourReview.setAttribute('id', 'yourReview');
      yourReview.appendChild(await construct_review(response['Review']));
      let editReviewButton = document.createElement('button');
      editReviewButton.setAttribute('class', 'btn btn-info');
      editReviewButton.innerHTML = 'Edit';
      editReviewButton.onclick = function() {
        document.querySelector('#yourReview').classList.add('d-none');
        document.querySelector('#edit-review').classList.remove('d-none');
        document.querySelector('#modal-review-input').value = response['Review'].review;
      }
      yourReview.appendChild(editReviewButton);

      review_list.appendChild(yourReview);

      let editReview = document.createElement('div');
      editReview.setAttribute('class', 'd-none');
      editReview.setAttribute('id', 'edit-review');
      editReview.innerHTML = '<textarea class="form-control" rows="4" placeholder="Review..." id="modal-review-input"></textarea><button type="button" class="btn btn-success" onclick="addReview()">Edit Review</button>';
      review_list.appendChild(editReview);
    } else if (response['Message'] == 'NOT REVIEWED') {
      review_list.innerHTML = '<textarea class="form-control" rows="4" placeholder="Review..." id="modal-review-input"></textarea><button type="button" class="btn btn-success" onclick="addReview()">Add Review</button>';
    }
    let h5 = document.createElement('h5');
    h5.innerHTML = 'Reviews';
    review_list.appendChild(h5);
  }

  let addReview = function() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        show_movie_by_id(current_movie);
      }
    };
    xhttp.open("POST", "<?php echo BACKEND . 'get_review.php';?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(
      'type=set&movie=' + current_movie +
      '&user=' + localStorage.getItem('JWT') +
      '&review=' + encodeURIComponent(document.querySelector('#modal-review-input').value)
    );
  }

  let addVote = function(review_id, vote) {
    if(checkLogin()) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          console.log(this.responseText);
        }
      };
      xhttp.open("POST", "<?php echo BACKEND . 'get_upvotes.php';?>", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send(
        'type=set' +
        '&user=' + localStorage.getItem('JWT') +
        '&review=' + review_id +
        '&vote=' + vote
      );
    } else {
      document.querySelector('#login-button').click();
    }
  }

  let voteReview = function(review_id, vote) {
    if(checkLogin()) {
      let upvoteButton = document.querySelector('#upvote_' + review_id);
      let downvoteButton = document.querySelector('#downvote_' + review_id);
      let voteCount = document.querySelector('#vote_count_' + review_id);
      upvoteButton.style.color = '#212529';
      downvoteButton.style.color = '#212529';
      if(upvotes[review_id] != undefined) {
        voteCount.innerHTML = voteCount.innerHTML - upvotes[review_id];
      }
      if(upvotes[review_id] != undefined && upvotes[review_id] == vote) {
        addVote(review_id, 0);
        upvotes[review_id] = 0;

      } else {
        addVote(review_id, vote);
        upvotes[review_id] = vote;
        if(vote == 1) {
          upvoteButton.style.color = '#FF8b60';
          voteCount.innerHTML = parseInt(voteCount.innerHTML) + 1;
        }
        if(vote == -1) {
          downvoteButton.style.color = '#9494FF';
          voteCount.innerHTML = voteCount.innerHTML - 1;
        }
      }
    } else {
      document.querySelector('#login-button').click();
    }
  }

  let construct_review = async function(review) {
    let li = document.createElement('li');
    li.setAttribute('class', 'list-group-item list-group-item-dark d-flex flex-column');

    let liHeader = document.createElement('span');
    liHeader.setAttribute('class', 'd-flex justify-content-between');
    liHeader.innerHTML = '<h5>' + review.username + '</h5>';
    if(review.rating != 0) {
      liHeader.innerHTML += '<h5>' + review.rating + '<i class="ml-2 fas fa-star"></i></h5>';
    }
    li.appendChild(liHeader);

    let liBody = document.createElement('span');
    liBody.setAttribute('class', 'text-justify');
    liBody.setAttribute('id', 'review_' + review.id);
    liBody.innerHTML = review.review;
    li.appendChild(liBody);

    let liUpvotes = document.createElement('span');
    liUpvotes.setAttribute('class', 'd-flex align-items-center');

    let upvoteButton = document.createElement('button');
    upvoteButton.setAttribute('type', 'button');
    upvoteButton.setAttribute('class', 'btn fas fa-arrow-up vote-button');
    upvoteButton.setAttribute('id', 'upvote_' + review.id);
    upvoteButton.onclick = function() {
      if(checkLogin()) {
        voteReview(review.id, 1);
        let userData = {
          'type': 2,
          'from': localStorage.getItem('username'),
          'movie': current_movie
        };
        let notifData = {
          'type': 'SEND',
          'to_user': review.username,
          'message': encodeURIComponent(JSON.stringify(userData))
        };
        socket.send(JSON.stringify(notifData));
      } else {
        document.querySelector('#login-button').click();
      }
    }

    let voteCount = document.createElement('span');
    voteCount.setAttribute('id', 'vote_count_' + review.id);
    voteCount.innerHTML = review.upvotes;

    let downvoteButton = document.createElement('button');
    downvoteButton.setAttribute('type', 'button');
    downvoteButton.setAttribute('class', 'btn fas fa-arrow-down vote-button');
    downvoteButton.setAttribute('id', 'downvote_' + review.id);
    downvoteButton.onclick = function() {
      if(checkLogin()) {
        voteReview(review.id, -1);
        let userData = {
          'type': 0,
          'from': localStorage.getItem('username'),
          'movie': current_movie
        };
        let notifData = {
          'type': 'SEND',
          'to_user': review.username,
          'message': encodeURIComponent(JSON.stringify(userData))
        };
        socket.send(JSON.stringify(notifData));
      } else {
        document.querySelector('#login-button').click();
      }
    }

    if(checkLogin()) {
      let vote = await getUserVote(review.id);
      upvotes[review.id] = vote;
      if(vote == 1) {
        upvoteButton.style.color = '#FF8b60';
      }
      if(vote == -1) {
        downvoteButton.style.color = '#9494FF';
      }
    }

    liUpvotes.appendChild(upvoteButton);
    liUpvotes.appendChild(voteCount);
    liUpvotes.appendChild(downvoteButton);

    li.appendChild(liUpvotes);

    return li;
  }

  let construct_item_thumb = function(item) {
    // Create Outer Container.
    let outerContainer = document.createElement('div');
    outerContainer.setAttribute('class', 'col-12 col-sm-6 col-md-4 col-lg-3 p-1 d-flex justify-content-center');

    // Create item wrapper
    let itemWrapper = document.createElement('div');
    itemWrapper.setAttribute('class', 'item-wrapper d-inline-block');

    // Poster Image.
    let posterImage = document.createElement('img');
    posterImage.setAttribute('class', 'poster-image d-inline');
    posterImage.setAttribute('src', item.poster);
    posterImage.setAttribute('alt', item.title);
    itemWrapper.appendChild(posterImage);

    // hover
    let posterHover = document.createElement('div');
    posterHover.setAttribute('class', 'poster-hover');

    let itemTitle = document.createElement('span');
    itemTitle.setAttribute('class', 'item-title');
    itemTitle.innerHTML = item.title;
    posterHover.appendChild(itemTitle);

    let itemDescription = document.createElement('span');
    itemDescription.setAttribute('class', 'item-description');
    itemDescription.innerHTML = item.synopsis;
    posterHover.appendChild(itemDescription);

    let itemRelease = document.createElement('span');
    itemRelease.setAttribute('class', 'item-release');
    itemRelease.innerHTML = item.year;
    posterHover.appendChild(itemRelease);

    let itemGenres = document.createElement('span');
    itemGenres.setAttribute('class', 'item-genre');
    itemGenres.innerHTML = item.genres;
    posterHover.appendChild(itemGenres);

    let viewButton = document.createElement('button');
    viewButton.setAttribute('class', 'btn btn-success');
    viewButton.onclick = function() {
      show_movie_details(item);
      document.querySelector('#modal-button').click();
    }

    viewButton.innerHTML = 'View More';
    posterHover.appendChild(viewButton);

    if(typeof(edit_movie_details) == typeof(Function)) {
      let editButton = document.createElement('button');
      editButton.setAttribute('class', 'btn btn-info');
      editButton.onclick = function() {
        reset_movie_details();
        edit_movie_details(item);
        document.querySelector('#edit-modal-button').click();
      }
      editButton.innerHTML = 'Edit Item';
      posterHover.appendChild(editButton);
    }

    itemWrapper.appendChild(posterHover);
    outerContainer.appendChild(itemWrapper);

    return outerContainer;
  }

  function toggleRating() {
    if(checkLogin()) {
      document.querySelector('#rating-toggle').classList.toggle('d-flex');
      document.querySelector('#rating-toggle').classList.toggle('d-none');
    } else {
      document.querySelector('#login-button').click();
    }
  }

  function setRating(rating = 0) {
    let e = document.querySelector('#rating-container').firstElementChild;
    for(let i = 1; i <= 10; i++) {
      if(i <= rating) {
        e.classList.remove('far');
        e.classList.add('fas');
      } else {
        e.classList.remove('fas');
        e.classList.add('far');
      }
      e = e.nextElementSibling;
    }
    if(current_movie_rating == 0) {
      document.querySelector('#rating-star').classList.remove('fas');
      document.querySelector('#rating-star').classList.add('far');
      document.querySelector('#rating-star-text').innerHTML = '<span>Rate</span><span>This</span>';
    } else {
      document.querySelector('#rating-star').classList.remove('far');
      document.querySelector('#rating-star').classList.add('fas');
      document.querySelector('#rating-star-text').innerHTML = '<h2 class="m-0">' + current_movie_rating + '</h2><span>You</span>';
    }
  }

  function initRating() {
    let ratingContainer = document.querySelector('#rating-container');
    ratingContainer.innerHTML = '';
    for(let i = 1; i <= 10; i++) {
      let ratingStar = document.createElement('button');
      ratingStar.setAttribute('type', 'button');
      ratingStar.setAttribute('class', 'btn p-0 border-0 fa-star text-light');
      ratingStar.onmouseover = function() {
        setRating(i);
      }
      ratingStar.onmouseout = function() {
        setRating(current_movie_rating);
      }
      ratingStar.onclick = function() {
        current_movie_rating = i;
        setRating(i);
        rate_movie();
      }
      ratingContainer.appendChild(ratingStar);
    }
  }

  initRating();

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  async function show_movie_details(item) {
    current_movie = item.id;

    document.querySelector('#modal-details-title').innerHTML = item.title;
    document.querySelector('#modal-details-genres').innerHTML = item.genres.split(',').join(' / ');
    document.querySelector('#modal-details-synopsis').innerHTML = item.synopsis;
    document.querySelector('#modal-details-year').innerHTML = item.year;
    document.querySelector('#modal-details-imdb-rating').innerHTML = item.imdb_rating;
    document.querySelector('#modal-details-mmdb-rating').innerHTML = await get_movie_rating();
    document.querySelector('#modal-details-trailer').setAttribute('src', "https://www.youtube.com/embed/" + item.yt_trailer_code + "?rel=0");
    document.querySelector('#modal-poster').setAttribute('src', item.poster);

    let downloadButtons = document.querySelector('#modal-details-download-links');
    downloadButtons.innerHTML = '';
    for(let torrent of item.torrents) {
      let downloadButton = document.createElement('a');
      downloadButton.setAttribute('class', 'btn btn-outline-success btn-sm');
      downloadButton.setAttribute('href', torrent.url);
      downloadButton.innerHTML = torrent.quality + " " + capitalizeFirstLetter(torrent.type);
      downloadButtons.appendChild(downloadButton);
    }

    get_similar_movies();

    document.querySelector('#modal-review-list').innerHTML = '';

    review_page = 1;

    upvotes = {};

    if(checkLogin()) {
      await getUserReview();

      current_movie_rating = await get_user_rating();

      setRating(current_movie_rating);
    }

    await getReviews();
  }

  function generate_similar_movie_thumb(item) {
    // Container;
    let container = document.createElement('div');
    container.setAttribute('class', 'col-6 col-sm-3 col-xl-6');

    let thumbnail = document.createElement('img');
    thumbnail.setAttribute('class', 'img-thumbnail m-2 cursor-pointer');
    thumbnail.setAttribute('src', item.poster);

    container.appendChild(thumbnail);
    container.onclick = function() {
      show_movie_details(item);
      document.querySelector('#detailsModal').scrollTop = 0;
    }

    return container;
  }

  function process_similar_movies(json_response) {
    let response = JSON.parse(json_response);
    if(response['Message'] != 'SUCCESS') {
        console.log(response);
        return;
    }
    let similar_movies_list = document.querySelector('#similar-movies-list');
    similar_movies_list.innerHTML = '';
    for(let item of response['Similar']) {
      similar_movies_list.appendChild(generate_similar_movie_thumb(item));
    }
  }

  function get_similar_movies() {
    return new Promise(function(resolve, reject) {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          process_similar_movies(this.responseText);
        }
      };
      xhttp.open("GET", "<?php echo BACKEND;?>similar_movies.php?movie_id=" + current_movie, true);
      xhttp.send();
    });
  }

  function process_show_movie(json_response) {
    let response = JSON.parse(json_response);
    if(response['Response'] == 'True') {
      let item = response['Search'][0];
      show_movie_details(item);
    }
  }

  function show_movie_by_id(id) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        process_show_movie(this.responseText);
        let modal = document.querySelector('#detailsModal');
        if(!modal.classList.contains('show')) {
          document.querySelector('#modal-button').click();
        }
      }
    };
    xhttp.open("GET", '<?php echo BACKEND; ?>search.php?id=' + id, true);
    xhttp.send();
  }
</script>
