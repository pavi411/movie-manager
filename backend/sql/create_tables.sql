CREATE TABLE `user_rating` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`activity_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`movie_id` INT NOT NULL,
	`rating` FLOAT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `user_recommendations` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`type` INT NOT NULL,
	`movie_1` INT NOT NULL,
	`movie_2` INT NOT NULL,
	`movie_3` INT NOT NULL,
	`movie_4` INT NOT NULL,
	`movie_5` INT NOT NULL,
	`movie_6` INT NOT NULL,
	`movie_7` INT NOT NULL,
	`movie_8` INT NOT NULL,
	`movie_9` INT NOT NULL,
	`movie_10` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `movie_recommendations` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`movie_id` INT NOT NULL,
	`movie_1` INT NOT NULL,
	`movie_2` INT NOT NULL,
	`movie_3` INT NOT NULL,
	`movie_4` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `user_reviews` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`activity_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`movie_id` INT NOT NULL,
	`review` VARCHAR(1024) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `user_upvotes` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`activity_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`review_id` INT NOT NULL,
	`type` BOOLEAN NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `user_activity` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`user_id` INT NOT NULL,
	`type` INT NOT NULL,
	`movie_id` INT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `user_notifications` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`activity_id` INT NOT NULL,
	PRIMARY KEY (`id`)
);

ALTER TABLE `user_rating` ADD CONSTRAINT `user_rating_fk0` FOREIGN KEY (`activity_id`) REFERENCES `user_activity`(`id`);

ALTER TABLE `user_rating` ADD CONSTRAINT `user_rating_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `user_rating` ADD CONSTRAINT `user_rating_fk2` FOREIGN KEY (`movie_id`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk0` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk1` FOREIGN KEY (`movie_1`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk2` FOREIGN KEY (`movie_2`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk3` FOREIGN KEY (`movie_3`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk4` FOREIGN KEY (`movie_4`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk5` FOREIGN KEY (`movie_5`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk6` FOREIGN KEY (`movie_6`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk7` FOREIGN KEY (`movie_7`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk8` FOREIGN KEY (`movie_8`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk9` FOREIGN KEY (`movie_9`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_recommendations` ADD CONSTRAINT `user_recommendations_fk10` FOREIGN KEY (`movie_10`) REFERENCES `movie_data`(`id`);

ALTER TABLE `movie_recommendations` ADD CONSTRAINT `movie_recommendations_fk0` FOREIGN KEY (`movie_id`) REFERENCES `movie_data`(`id`);

ALTER TABLE `movie_recommendations` ADD CONSTRAINT `movie_recommendations_fk1` FOREIGN KEY (`movie_1`) REFERENCES `movie_data`(`id`);

ALTER TABLE `movie_recommendations` ADD CONSTRAINT `movie_recommendations_fk2` FOREIGN KEY (`movie_2`) REFERENCES `movie_data`(`id`);

ALTER TABLE `movie_recommendations` ADD CONSTRAINT `movie_recommendations_fk3` FOREIGN KEY (`movie_3`) REFERENCES `movie_data`(`id`);

ALTER TABLE `movie_recommendations` ADD CONSTRAINT `movie_recommendations_fk4` FOREIGN KEY (`movie_4`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_reviews` ADD CONSTRAINT `user_reviews_fk0` FOREIGN KEY (`activity_id`) REFERENCES `user_activity`(`id`);

ALTER TABLE `user_reviews` ADD CONSTRAINT `user_reviews_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `user_reviews` ADD CONSTRAINT `user_reviews_fk2` FOREIGN KEY (`movie_id`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_upvotes` ADD CONSTRAINT `user_upvotes_fk0` FOREIGN KEY (`activity_id`) REFERENCES `user_activity`(`id`);

ALTER TABLE `user_upvotes` ADD CONSTRAINT `user_upvotes_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `user_upvotes` ADD CONSTRAINT `user_upvotes_fk2` FOREIGN KEY (`review_id`) REFERENCES `user_reviews`(`id`);

ALTER TABLE `user_activity` ADD CONSTRAINT `user_activity_fk0` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `user_activity` ADD CONSTRAINT `user_activity_fk1` FOREIGN KEY (`movie_id`) REFERENCES `movie_data`(`id`);

ALTER TABLE `user_notifications` ADD CONSTRAINT `user_notifications_fk0` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);

ALTER TABLE `user_notifications` ADD CONSTRAINT `user_notifications_fk1` FOREIGN KEY (`activity_id`) REFERENCES `user_activity`(`id`);
