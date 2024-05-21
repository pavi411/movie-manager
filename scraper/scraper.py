import requests
import mysql.connector

mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="",
  database="movie_manager"
)

list_api_endpoint = 'https://yts.lt/api/v2/list_movies.json'

movie_data_query = ('INSERT INTO movie_data ('
                    'imdb_code, title, synopsis, genres, language, mpa_rating, '
                    'imdb_rating, runtime, year, poster, yt_trailer_code) '
                    'VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)')

torrent_data_query = ('INSERT INTO torrent_data ('
                      'movie_id, url, quality, type, size) '
                      'VALUES (%s, %s, %s, %s, %s)')

# Get total movie count.
r = requests.get(url = list_api_endpoint, params='limit=1')
data = r.json()
if data['status'] == 'ok' and data['status_message'] == 'Query was successful':
    movie_count = data['data']['movie_count']

limit = 50
page = 0
inserted = 0

while movie_count > limit*page:
    page += 1
    params = {'limit': limit, 'page': page}
    r = requests.get(url = list_api_endpoint, params=params)
    data = r.json()

    mycursor = mydb.cursor()

    if data['status'] == 'ok' and data['status_message'] == 'Query was successful':
        for movie in data['data']['movies']:
            try:
                movie_data = (
                    movie['imdb_code'],
                    movie['title'],
                    movie['synopsis'],
                    ','.join(movie['genres']),
                    movie['language'],
                    movie['mpa_rating'],
                    movie['rating'],
                    movie['runtime'],
                    movie['year'],
                    movie['large_cover_image'],
                    movie['yt_trailer_code']
                )

                mycursor.execute(movie_data_query, movie_data)

                mydb.commit()

                movie_id = mycursor.lastrowid

                torrent_data = []
                for torrent in movie['torrents']:
                    torrent_data.append((
                        movie_id,
                        torrent['url'],
                        torrent['quality'],
                        torrent['type'],
                        torrent['size']
                    ))

                mycursor.executemany(torrent_data_query, torrent_data)

                mydb.commit()

                inserted += 1
                print(inserted)
            except Exception as e:
                print(e)
