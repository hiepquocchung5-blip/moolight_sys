-- Moonlight Digital Market - Core Database Schema
-- Uses `md_` prefix for enhanced security through obscurity

-- 1. Global Currency Rates (Controlled by MoonAdmin)
CREATE TABLE md_exchange_rates (
    rate_id INT AUTO_INCREMENT PRIMARY KEY,
    currency_code VARCHAR(5) NOT NULL UNIQUE, -- 'USD', 'EUR', 'THB'
    mmk_value DECIMAL(10, 2) NOT NULL,        -- e.g., 4200.00
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert defaults
INSERT INTO md_exchange_rates (currency_code, mmk_value) VALUES 
('USD', 4200.00), ('EUR', 4500.00), ('THB', 130.00), ('SGD', 3100.00);

-- 2. Citizens (Users / Moon Accounts)
CREATE TABLE md_citizens (
    cit_id INT AUTO_INCREMENT PRIMARY KEY,
    moon_tag VARCHAR(50) NOT NULL UNIQUE,     -- e.g., Moon_9921
    email VARCHAR(150) NOT NULL UNIQUE,
    pass_hash VARCHAR(255) NOT NULL,
    auth_google_uid VARCHAR(100) DEFAULT NULL,
    auth_tg_uid VARCHAR(100) DEFAULT NULL,
    rank ENUM('guest', 'verified', 'premium', 'admin') DEFAULT 'guest',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Artifacts (Products & Premium Plans)
CREATE TABLE md_artifacts (
    art_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(100),                    -- 'gaming', 'streaming', 'ai_plans'
    delivery_type ENUM('singularity', 'infinity', 'bespoke') NOT NULL,
    base_price_mmk DECIMAL(12, 2) NOT NULL,   -- Always stored in MMK
    is_active BOOLEAN DEFAULT TRUE
);

-- 4. Vault Keys (Inventory for Singularity/Infinity items)
CREATE TABLE md_vault_keys (
    key_id INT AUTO_INCREMENT PRIMARY KEY,
    art_id INT NOT NULL,
    payload TEXT NOT NULL,                    -- The PIN, password, or link
    is_claimed BOOLEAN DEFAULT FALSE,
    claimed_by_cit_id INT DEFAULT NULL,
    FOREIGN KEY (art_id) REFERENCES md_artifacts(art_id),
    FOREIGN KEY (claimed_by_cit_id) REFERENCES md_citizens(cit_id)
);

-- 5. Neural Sparks (AI Prompts)
CREATE TABLE md_neural_sparks (
    spark_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,                    -- The actual prompt text/instructions
    preview_img VARCHAR(255) DEFAULT NULL,    -- Watermarked preview image
    type ENUM('image', 'video', 'study', 'cyber_research') NOT NULL,
    is_free BOOLEAN DEFAULT FALSE,
    price_mmk DECIMAL(10, 2) DEFAULT 0.00
);

-- 6. Treasury Ledgers (Orders / Cart)
CREATE TABLE md_treasury_ledgers (
    ledger_id INT AUTO_INCREMENT PRIMARY KEY,
    cit_id INT NOT NULL,
    total_mmk DECIMAL(12, 2) NOT NULL,
    display_currency VARCHAR(5) DEFAULT 'MMK', -- What the user saw at checkout
    status ENUM('pending', 'verifying', 'complete', 'expired') DEFAULT 'pending',
    checkout_expires_at DATETIME NOT NULL,     -- 10 min session limit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cit_id) REFERENCES md_citizens(cit_id)
);

-- 7. Transaction Proofs (Manual Payment Verification)
CREATE TABLE md_txn_proofs (
    proof_id INT AUTO_INCREMENT PRIMARY KEY,
    ledger_id INT NOT NULL,
    pay_method ENUM('KBZPay', 'AYAPay', 'UABPay', 'PayPal', 'Visa', 'Mastercard') NOT NULL,
    txn_last_6 VARCHAR(10) NOT NULL,
    proof_image_path VARCHAR(255) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ledger_id) REFERENCES md_treasury_ledgers(ledger_id)
);

-- 8. Whisper Threads (Admin-User Support & Bespoke Forms)
CREATE TABLE md_whisper_threads (
    thread_id INT AUTO_INCREMENT PRIMARY KEY,
    cit_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    message_payload TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cit_id) REFERENCES md_citizens(cit_id)
);