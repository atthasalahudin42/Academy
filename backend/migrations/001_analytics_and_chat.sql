-- Academy.AI: Analytics, Practice, and AI Chat tables
-- Run against MySQL database `academy` if using DATABASE_URL=mysql+pymysql://...
-- SQLite users: tables are auto-created via backend/database/init_db.py or FastAPI startup

CREATE TABLE IF NOT EXISTS practice_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    topic VARCHAR(255) NOT NULL,
    question_type VARCHAR(50),
    difficulty VARCHAR(20),
    num_questions INT,
    score FLOAT DEFAULT 0,
    total_questions INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ps_user (user_id),
    INDEX idx_ps_created (created_at)
);

CREATE TABLE IF NOT EXISTS practice_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    question_text TEXT NOT NULL,
    options JSON,
    correct_answer TEXT NOT NULL,
    explanation TEXT,
    FOREIGN KEY (session_id) REFERENCES practice_sessions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS practice_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    user_id INT NULL,
    selected_answer TEXT NOT NULL,
    is_correct BOOLEAN,
    ai_feedback TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES practice_questions(id) ON DELETE CASCADE,
    INDEX idx_pa_user (user_id)
);

CREATE TABLE IF NOT EXISTS performance_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    total_sessions INT DEFAULT 0,
    total_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    average_accuracy FLOAT DEFAULT 0,
    study_time_minutes INT DEFAULT 0,
    study_streak INT DEFAULT 0,
    total_xp INT DEFAULT 0,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ai_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    prompt TEXT NOT NULL,
    response TEXT NOT NULL,
    model_used VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ai_user (user_id),
    INDEX idx_ai_created (created_at)
);
