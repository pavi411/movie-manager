import numpy as np

import requests
import mysql.connector

mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="",
  database="movie_manager"
)

mycursor = mydb.cursor()

genres = [
    'Action','Adventure','Animation','Biography','Comedy','Crime','Documentary','Drama','Family','Fantasy','Film-Noir','Game-Show','History','Horror','Music','Musical','Mystery','News','Reality-TV','Romance','Sci-Fi','Sport','Talk-Show','Thriller','War','Western'
]

def get_ratings():
    movie_data_query = 'SELECT id, genres, imdb_rating FROM movie_data ORDER BY id ASC'
    mycursor.execute(movie_data_query)
    movie_data = mycursor.fetchall()
    movie_ids = {movie_data[i][0]: i for i in range(len(movie_data))}
    movie_data_matrix = np.zeros((len(movie_ids), len(genres)), dtype=np.int8)
    imdb_ratings = []
    for movie in movie_data:
        for genre in movie[1].split(','):
            movie_data_matrix[movie_ids[movie[0]]][genres.index(genre)] = 1;

        imdb_ratings.append(movie[2])

    user_data_query = 'SELECT id FROM users ORDER BY id ASC'
    mycursor.execute(user_data_query)
    user_data = mycursor.fetchall()
    user_ids = {user_data[i][0]: i for i in range(len(user_data))}

    rating_data_query = 'SELECT * FROM user_rating'
    mycursor.execute(rating_data_query)
    ratings = [[i[2], i[3], i[4]] for i in mycursor]

    rating_matrix = np.zeros((len(user_ids) + 1, len(movie_ids)))

    for rating in ratings:
        rating_matrix[user_ids[rating[0]], movie_ids[rating[1]]] = rating[2]

    rating_matrix[-1] = imdb_ratings

    movie_ids_inverted = {v: k for k, v in movie_ids.items()}
    user_ids_inverted = {v: k for k, v in user_ids.items()}

    return movie_ids_inverted, user_ids_inverted, movie_data_matrix, imdb_ratings, rating_matrix;

def set_user_recommendations(user, type, predictions):
    #Check if user already has recommendations.
    mycursor.execute('SELECT EXISTS(SELECT * FROM user_recommendations WHERE user_id = ' + str(user) + ' AND type = ' + str(type) + ')')
    user_exists = mycursor.fetchall()[0][0] == True;

    if user_exists:
        sql = 'UPDATE user_recommendations SET ' + ', '.join(['movie_' + str(i+1) + ' = ' + str(predictions[i]) for i in range(10)]) + ' WHERE user_id = ' + str(user) + ' AND type = ' + str(type)
    else:
        sql = 'INSERT INTO user_recommendations (user_id, type, ' + ', '.join(['movie_' + str(i+1) for i in range(10)]) + ') VALUES (' + str(user) + ', ' + str(type) + ', ' + ', '.join([str(i) for i in predictions]) + ')'

    mycursor.execute(sql)
    mydb.commit()

def set_movie_recommendations(movie, predictions):
    mycursor.execute('SELECT EXISTS(SELECT * FROM movie_recommendations WHERE movie_id = ' + str(movie) + ')')
    user_exists = mycursor.fetchall()[0][0] == True;

    if user_exists:
        sql = 'UPDATE movie_recommendations SET ' + ', '.join(['movie_' + str(i+1) + ' = ' + str(predictions[i]) for i in range(4)]) + ' WHERE movie_id = ' + str(movie)
    else:
        sql = 'INSERT INTO movie_recommendations (movie_id, ' + ', '.join(['movie_' + str(i+1) for i in range(4)]) + ') VALUES (' + str(movie) + ', ' + ', '.join([str(i) for i in predictions]) + ')'

    mycursor.execute(sql)
    mydb.commit()
