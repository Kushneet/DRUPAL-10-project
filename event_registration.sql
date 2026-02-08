CREATE TABLE event_registration_event (
  id INT AUTO_INCREMENT PRIMARY KEY,
  registration_start DATE NOT NULL,
  registration_end DATE NOT NULL,
  event_date DATE NOT NULL,
  event_name VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  created INT NOT NULL
);
CREATE TABLE event_registration_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  event_date DATE NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  college VARCHAR(255) NOT NULL,
  department VARCHAR(255) NOT NULL,
  created INT NOT NULL
);
CREATE INDEX idx_email_event_date
ON event_registration_entries (email, event_date);
