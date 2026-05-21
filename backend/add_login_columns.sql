-- Fixed MySQL 8.4 syntax: Separate ALTER statements
USE academy;

ALTER TABLE users ADD COLUMN login_token VARCHAR(64) NULL;
ALTER TABLE users ADD COLUMN login_token_expiry DATETIME NULL;
ALTER TABLE users ADD COLUMN token_expiry DATETIME NULL;

ALTER TABLE users ADD INDEX idx_login_token (login_token);
ALTER TABLE users ADD INDEX idx_verification_token (verification_token);
