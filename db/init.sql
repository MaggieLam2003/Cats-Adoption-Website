-- Create the cats table
CREATE TABLE cats (
  id INTEGER NOT NULL UNIQUE PRIMARY KEY AUTOINCREMENT,
  cat_name TEXT NOT NULL,
  cat_description TEXT NOT NULL,
  file_name TEXT NOT NULL,
  file_ext TEXT NOT NULL,
  source TEXT
);

INSERT INTO cats (cat_name, cat_description, file_name, file_ext, source)
VALUES
  ('Whiskers', 'A fluffy orange tabby who loves to nap in the sun', 'cat1.jpeg','jpeg', 'https://unsplash.com/s/photos/cute-cat'),
  ('Smokey',  'A sleek black cat who enjoys playing with toy mice', 'cat2.jpeg', 'jpeg', 'https://www.vecteezy.com/free-photos/cat'),
  ('Mochi',  'A white cat that is extra playful', 'cat3.jpeg', 'jpeg', 'https://www.verywellmind.com/are-some-people-really-afraid-of-cats-2671757'),
  ('Mittens', 'A white and grey tuxedo cat who loves attention', 'cat4.jpeg', 'jpeg', 'https://hakaimagazine.com/features/its-10-pm-do-you-know-where-your-cat-is/'),
  ('Luna', 'A black and white cat with a playful personality', 'cat5.jpeg','jpeg','https://www.nature.com/articles/494009a'),
  ('Simba', 'An orange tabby with a love for exploring', 'cat6.jpeg','jpeg','https://www.cats.org.uk/');

CREATE TABLE tags (
  id INTEGER NOT NULL UNIQUE PRIMARY KEY AUTOINCREMENT,
  tag_type TEXT NOT NULL,
  tag_value TEXT NOT NULL
);

INSERT INTO tags (tag_type, tag_value) VALUES
-- 1-4 age
  ('age', 'Kitten'),
  ('age', 'Adult'),
  ('age', 'Senior'),
  ('age', 'Junior'),
-- 5-9 color
  ('color', 'Black'),
  ('color', 'White'),
  ('color', 'Orange'),
  ('color', 'Gray'),
  ('color', 'Brown'),
-- 10-14 breed
  ('breed', 'Siamese'),
  ('breed', 'Persian'),
  ('breed', 'Bengal'),
  ('breed', 'Balinese'),
  ('breed', 'Sphynx'),
-- 15-16 gender
  ('gender', 'Male'),
  ('gender', 'Female');

CREATE TABLE cats_tags (
  id INTEGER NOT NULL UNIQUE PRIMARY KEY AUTOINCREMENT,
  cat_id INTEGER NOT NULL,
  tag_id INTEGER,
  FOREIGN KEY (cat_id) REFERENCES cats(id),
  FOREIGN KEY (tag_id) REFERENCES tags(id)
);

-- Whiskers
INSERT INTO cats_tags (cat_id, tag_id)
VALUES
  (1, 1), -- Kitten
  (1, 5), -- Black
  (1, 10), -- Siamese
  (1, 15); -- Male

-- Smokey tags
INSERT INTO cats_tags (cat_id, tag_id)
VALUES
  (2, 2), -- Adult
  (2, 6), -- White
  (2, 11), -- Persian
  (2, 16); -- Female

-- Mochi tags
INSERT INTO cats_tags (cat_id, tag_id)
VALUES
  (3, 3), -- Senior
  (3, 7), -- Orange
  (3, 13), -- Banlinese
  (3, 16); -- Female


-- Mittens tags
INSERT INTO cats_tags (cat_id, tag_id)
VALUES
  (4, 4), -- Junior
  (4, 8), -- Gray
  (4, 14), -- Sphynx
  (4, 15); -- Male

-- Luna tags
INSERT INTO cats_tags (cat_id, tag_id)
VALUES
  (5, 1), -- Kitten
  (5, 9), -- Brown
  (5, 12), -- Bengal
  (5, 16); -- Female

  -- Simba tags
INSERT INTO cats_tags (cat_id, tag_id)
VALUES
  (6, 2), -- Adult
  (6, 5), -- Black
  (6, 11), -- Persian
  (6, 15); -- Male



-- LOGIN TABLES

CREATE TABLE users (
  id INTEGER NOT NULL UNIQUE,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  username TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  PRIMARY KEY(id AUTOINCREMENT)
);

INSERT INTO
  users (id, name, email, username, password)
VALUES
  (
    1,
    'Maggie Lam',
    'maggie@cornell.edu',
    'maggie',
    '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.' -- monkey
  );

CREATE TABLE sessions (
  id INTEGER NOT NULL UNIQUE,
  user_id INTEGER NOT NULL,
  session TEXT NOT NULL UNIQUE,
  last_login TEXT NOT NULL,
  PRIMARY KEY(id AUTOINCREMENT) FOREIGN KEY(user_id) REFERENCES users(id)
);
