-- =========================
-- USUARIOS GENERALES
-- =========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','student','company') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- ESTUDIANTES
-- =========================
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    institutional_email VARCHAR(150),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =========================
-- EMPRESAS
-- =========================
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) UNIQUE
);

-- =========================
-- USUARIOS DE EMPRESA
-- =========================
CREATE TABLE company_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    company_id INT,
    contact_name VARCHAR(150),
    contact_position VARCHAR(100),
    contact_email VARCHAR(150),
    contact_phone VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- =========================
-- CVS
-- =========================
CREATE TABLE cvs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    photo VARCHAR(255),
    full_name VARCHAR(150),
    phone VARCHAR(50),
    email VARCHAR(150),
    gender VARCHAR(20),
    age INT,
    level ENUM('estudiante','egresado'),
    year INT NULL,
    resume TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

CREATE TABLE cv_careers (
    cv_id INT,
    career_id INT,
    PRIMARY KEY (cv_id, career_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE,
    FOREIGN KEY (career_id) REFERENCES careers(id)
);

-- =========================
-- LINKS DEL CV
-- =========================
CREATE TABLE cv_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cv_id INT,
    type ENUM('linkedin','github','portfolio','website','other'),
    url VARCHAR(255),
    FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE
);

-- =========================
-- EXPERIENCIA
-- =========================
CREATE TABLE experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cv_id INT,
    company_name VARCHAR(150),
    position VARCHAR(100),
    start_date DATE,
    end_date DATE,
    description TEXT,
    FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE
);

-- =========================
-- FORMACION COMPLEMENTARIA
-- =========================
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cv_id INT,
    institution VARCHAR(150),
    program_name VARCHAR(150),
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE
);

-- =========================
-- CATEGORÍAS DE SKILLS
-- =========================
CREATE TABLE skill_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE -- Ej: 'Soft Skills', 'Technical Skills', 'Languages'
);

-- =========================
-- SKILLS (ACTUALIZADA)
-- =========================
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT, -- Relación con la categoría
    name VARCHAR(100) UNIQUE NOT NULL,
    FOREIGN KEY (category_id) REFERENCES skill_categories(id) ON DELETE SET NULL
);

-- RELACION CV - SKILLS
CREATE TABLE cv_skills (
    cv_id INT,
    skill_id INT,
    PRIMARY KEY (cv_id, skill_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id)
);

-- =========================
-- CARRERAS (CATALOGO)
-- =========================
-- CATEGORÍAS DE CARRERAS
-- =========================
CREATE TABLE career_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE -- Ej: 'Ingenierías', 'Ciencias Sociales', 'Salud'
);

-- =========================
-- CARRERAS (ACTUALIZADA)
-- =========================
CREATE TABLE careers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT, -- Relación con la categoría
    name VARCHAR(150) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES career_categories(id) ON DELETE SET NULL
);

-- =========================
-- OPORTUNIDADES
-- =========================
CREATE TABLE opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_user_id INT,
    title VARCHAR(150),
    type_opor ENUM('pasantia','trabajo'),
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    remuneration DECIMAL(10,2),
    salary_visible BOOLEAN,
    vacancies INT,
    deadline DATE,
    functions TEXT,
    modality ENUM('presencial','semi','remoto'),
    schedule VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_user_id) REFERENCES company_users(id)
);

-- =========================
-- RELACION OPORTUNIDAD - CARRERAS
-- =========================
CREATE TABLE opportunity_careers (
    opportunity_id INT,
    career_id INT,
    PRIMARY KEY (opportunity_id, career_id),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (career_id) REFERENCES careers(id)
);

-- =========================
-- NIVELES PERMITIDOS
-- =========================
CREATE TABLE opportunity_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT,
    level ENUM('estudiante','egresado'),
    year INT NULL,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
);

-- =========================
-- SKILLS REQUERIDOS
-- =========================
CREATE TABLE opportunity_skills (
    opportunity_id INT,
    skill_id INT,
    PRIMARY KEY (opportunity_id, skill_id),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id)
);

-- =========================
-- MATCH (ALGORITMO)
-- =========================
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cv_id INT,
    opportunity_id INT,
    match_score DECIMAL(5,2), -- porcentaje
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_id) REFERENCES cvs(id),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id)
);

-- =========================
-- APLICACIONES
-- =========================
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cv_id INT,
    opportunity_id INT,
    status ENUM('aplicado','revision','rechazado','aceptado') DEFAULT 'aplicado',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_id) REFERENCES cvs(id),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id)
);

-- =========================
-- HISTORIAL DE CAMBIOS DE ESTADO
-- =========================
CREATE TABLE application_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT,
    changed_by INT, -- company_user_id
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (changed_by) REFERENCES company_users(id)
); 

--=============
--INSERTS

INSERT INTO skill_categories (name) VALUES
('Technical Skills'),
('Soft Skills'),
('Languages');

INSERT INTO skills (category_id, name) VALUES
-- Technical
(1, 'JavaScript'),
(1, 'Python'),
(1, 'SQL'),
(1, 'React'),
(1, 'Node.js'),
(1, 'Docker'),

-- Soft
(2, 'Comunicación'),
(2, 'Trabajo en equipo'),
(2, 'Pensamiento crítico'),

-- Languages
(3, 'Inglés'),
(3, 'Español'),
(3, 'Francés');


 INSERT INTO career_categories (name) VALUES
('Ingenierías'),
('Negocios'),
('Diseño'),
('Ciencias Sociales');

INSERT INTO careers (category_id, name) VALUES
-- Ingenierías
(1, 'Ingeniería en Sistemas'),
(1, 'Ingeniería en Software'),
(1, 'Ingeniería Industrial'),
(1, 'Ingeniería en Datos'),

-- Negocios
(2, 'Administración de Empresas'),
(2, 'Marketing'),
(2, 'Contaduría Pública'),

-- Diseño
(3, 'Diseño Gráfico'),
(3, 'Diseño UX/UI'),

-- Ciencias Sociales
(4, 'Psicología'),
(4, 'Comunicación Social'),
(4, 'Relaciones Internacionales');

INSERT INTO companies (name) VALUES
('Google'),
('Microsoft'),
('Amazon'),
('Meta'),
('Netflix'),
('Tesla'),
('Spotify'),
('Airbnb'),
('Uber'),
('Oracle'),
('IBM'),
('Salesforce');