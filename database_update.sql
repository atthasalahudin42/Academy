-- Add email verification columns
ALTER TABLE users ADD COLUMN is_verified TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN verification_token VARCHAR(64) NULL;

-- Optional: Verify existing users
UPDATE users SET is_verified = 1 WHERE id > 0;
