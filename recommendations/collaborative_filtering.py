import pandas as pd
import numpy as np

import db_helper

from sklearn.metrics.pairwise import pairwise_distances

def collaborative_filtering():
    print('Running collaborative_filtering...');
    
    movie_ids, user_ids, movie_data, movie_ratings, ratings = db_helper.get_ratings();

    user_similarity = pairwise_distances(ratings, metric='cosine')

    mean_user_rating = ratings.mean(axis=1)
    ratings_diff = (ratings - mean_user_rating[:, np.newaxis])
    user_prediction = mean_user_rating[:, np.newaxis] + user_similarity.dot(ratings_diff) / np.array([np.abs(user_similarity).sum(axis=1)]).T

    item_similarity = pairwise_distances(ratings.T, metric='cosine')
    genre_similarity = pairwise_distances(movie_data, metric='cosine')
    total_similarity = 2 - item_similarity - genre_similarity;
    item_prediction = ratings.dot(item_similarity) / np.array([np.abs(item_similarity).sum(axis=1)])

    for user in range(len(user_prediction) -1):
        for i in range(len(user_prediction[user])):
            if(ratings[user][i] != 0):
                user_prediction[user][i] = -1
        top_ten = np.argpartition(user_prediction[user], -10)[-10:]
        recommended = []
        for movie in top_ten[np.argsort(-top_ten)]:
            recommended.append(movie_ids[movie])
        db_helper.set_user_recommendations(user_ids[user], 0, recommended)

    for user in range(len(item_prediction) - 1):
        for i in range(len(item_prediction[user])):
            if(ratings[user][i] != 0):
                item_prediction[user][i] = -1
        top_ten = np.argpartition(item_prediction[user], -10)[-10:]
        recommended = []
        for movie in top_ten[np.argsort(-top_ten)]:
            recommended.append(movie_ids[movie])
        db_helper.set_user_recommendations(user_ids[user], 1, recommended)

    for item in range(len(total_similarity)):
        top_ten = np.argpartition(total_similarity[item], -25)[-25:]
        recommended = []
        rated = np.array([movie_ratings[i] for i in top_ten])
        for movie in top_ten[np.argsort(-rated)]:
            if item != movie:
                recommended.append(movie_ids[movie])
        db_helper.set_movie_recommendations(movie_ids[item], recommended[:4])

while 1:
    collaborative_filtering()
