-- Create Membres table
CREATE TABLE IF NOT EXISTS membres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(50),
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE
);

-- Insert default membre with id=1
INSERT INTO membres (id, nom, prenom, email, telephone, actif)
VALUES (1, 'Doe', 'John', 'john.doe@example.com', '+1234567890', TRUE)
ON DUPLICATE KEY UPDATE 
    nom = VALUES(nom),
    prenom = VALUES(prenom);
