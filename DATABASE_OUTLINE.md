# MIMS Database Schema Outline

## Core Tables

### 1. users
```
- id (bigint, PK, auto_increment)
- username (varchar(50), unique)
- email (varchar(100), unique)
- password (varchar(255))
- role_id (bigint, FK)
- branch_id (bigint, FK, nullable)
- two_factor_enabled (boolean, default: false)
- two_factor_secret (varchar(255), nullable)
- last_login_at (timestamp, nullable)
- login_attempts (int, default: 0)
- locked_until (timestamp, nullable)
- active (boolean, default: true)
- remember_token (varchar(100), nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable)
```

### 2. customers
```
- id (bigint, PK, auto_increment)
- customer_number (varchar(20), unique)
- first_name (varchar(100))
- last_name (varchar(100))
- full_name (varchar(200))
- nic (varchar(20), unique)
- date_of_birth (date)
- gender (enum: ['male', 'female', 'other'])
- marital_status (enum: ['single', 'married', 'divorced', 'widowed'])
- address_line_1 (varchar(255))
- address_line_2 (varchar(255), nullable)
- city (varchar(100))
- district (varchar(100))
- postal_code (varchar(10))
- mobile_number (varchar(15))
- home_phone (varchar(15), nullable)
- email (varchar(100), nullable)
- occupation (varchar(100), nullable)
- monthly_income (decimal(15,2), nullable)
- agent_id (bigint, FK)
- branch_id (bigint, FK)
- kyc_status (enum: ['pending', 'verified', 'rejected'])
- kyc_verified_at (timestamp, nullable)
- kyc_verified_by (bigint, FK, nullable)
- status (enum: ['active', 'inactive', 'blacklisted'])
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable)
```

### 3. accounts
```
- id (bigint, PK, auto_increment)
- account_number (varchar(20), unique)
- account_type_id (bigint, FK)
- customer_id (bigint, FK)
- branch_id (bigint, FK)
- current_balance (decimal(15,2), default: 0.00)
- available_balance (decimal(15,2), default: 0.00)
- minimum_balance (decimal(15,2))
- is_joint (boolean, default: false)
- status (enum: ['active', 'dormant', 'frozen', 'closed'])
- dormant_date (date, nullable)
- opened_date (date)
- closed_date (date, nullable)
- closed_by (bigint, FK, nullable)
- closure_reason (text, nullable)
- last_transaction_date (date, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable)
```

### 4. account_types
```
- id (bigint, PK, auto_increment)
- name (varchar(50), unique)
- code (varchar(10), unique)
- description (text, nullable)
- minimum_age (int, nullable)
- maximum_age (int, nullable)
- minimum_balance (decimal(15,2))
- interest_rate (decimal(5,2))
- allow_joint (boolean, default: false)
- max_withdrawals_per_month (int, nullable)
- active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
```

### 5. transactions
```
- id (bigint, PK, auto_increment)
- transaction_number (varchar(30), unique)
- account_id (bigint, FK)
- transaction_type (enum: ['deposit', 'withdrawal', 'transfer', 'interest', 'fee', 'reversal'])
- amount (decimal(15,2))
- balance_before (decimal(15,2))
- balance_after (decimal(15,2))
- reference_number (varchar(50), unique)
- description (varchar(255), nullable)
- transaction_date (date)
- transaction_time (time)
- related_account_id (bigint, FK, nullable)
- related_transaction_id (bigint, FK, nullable)
- performed_by (bigint, FK)
- branch_id (bigint, FK)
- status (enum: ['pending', 'completed', 'failed', 'reversed'])
- reversed_at (timestamp, nullable)
- reversed_by (bigint, FK, nullable)
- reversal_reason (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 6. fixed_deposits
```
- id (bigint, PK, auto_increment)
- fd_number (varchar(20), unique)
- account_id (bigint, FK)
- principal_amount (decimal(15,2))
- interest_rate (decimal(5,2))
- term_months (int)
- maturity_date (date)
- interest_payment_frequency (enum: ['monthly', 'quarterly', 'maturity'])
- total_interest_earned (decimal(15,2), default: 0.00)
- last_interest_paid_date (date, nullable)
- next_interest_date (date)
- status (enum: ['active', 'matured', 'closed', 'premature_closed'])
- opened_date (date)
- closed_date (date, nullable)
- premature_penalty (decimal(15,2), nullable)
- auto_renew (boolean, default: false)
- created_by (bigint, FK)
- closed_by (bigint, FK, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Relationship Tables

### 7. joint_account_holders
```
- id (bigint, PK, auto_increment)
- account_id (bigint, FK)
- customer_id (bigint, FK)
- is_primary (boolean, default: false)
- can_operate (boolean, default: true)
- added_date (date)
- added_by (bigint, FK)
- removed_date (date, nullable)
- removed_by (bigint, FK, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 8. customer_documents
```
- id (bigint, PK, auto_increment)
- customer_id (bigint, FK)
- document_type (enum: ['nic', 'passport', 'driving_license', 'utility_bill', 'other'])
- document_number (varchar(50), nullable)
- file_name (varchar(255))
- file_path (varchar(500))
- file_size (int)
- mime_type (varchar(100))
- verified (boolean, default: false)
- verified_by (bigint, FK, nullable)
- verified_at (timestamp, nullable)
- uploaded_by (bigint, FK)
- created_at (timestamp)
- updated_at (timestamp)
```

## Administrative Tables

### 9. branches
```
- id (bigint, PK, auto_increment)
- branch_code (varchar(10), unique)
- branch_name (varchar(100))
- address (text)
- city (varchar(100))
- district (varchar(100))
- phone (varchar(15))
- email (varchar(100))
- manager_id (bigint, FK, nullable)
- active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
```

### 10. roles
```
- id (bigint, PK, auto_increment)
- name (varchar(50), unique)
- display_name (varchar(100))
- description (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 11. permissions
```
- id (bigint, PK, auto_increment)
- name (varchar(100), unique)
- display_name (varchar(100))
- module (varchar(50))
- description (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 12. role_permissions
```
- id (bigint, PK, auto_increment)
- role_id (bigint, FK)
- permission_id (bigint, FK)
- created_at (timestamp)
- UNIQUE KEY (role_id, permission_id)
```

## System Tables

### 13. interest_rates
```
- id (bigint, PK, auto_increment)
- account_type_id (bigint, FK)
- rate (decimal(5,2))
- effective_from (date)
- effective_to (date, nullable)
- fd_term_months (int, nullable)
- created_by (bigint, FK)
- approved_by (bigint, FK, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 14. interest_calculations
```
- id (bigint, PK, auto_increment)
- account_id (bigint, FK)
- calculation_date (date)
- calculation_month (varchar(7))
- minimum_balance (decimal(15,2))
- interest_rate (decimal(5,2))
- interest_amount (decimal(15,2))
- days_in_month (int)
- transaction_id (bigint, FK, nullable)
- status (enum: ['calculated', 'credited', 'failed'])
- created_at (timestamp)
- updated_at (timestamp)
```

### 15. audit_logs
```
- id (bigint, PK, auto_increment)
- user_id (bigint, FK, nullable)
- action (varchar(100))
- auditable_type (varchar(100))
- auditable_id (bigint)
- old_values (json, nullable)
- new_values (json, nullable)
- ip_address (varchar(45))
- user_agent (varchar(500))
- tags (json, nullable)
- created_at (timestamp)
```

### 16. system_settings
```
- id (bigint, PK, auto_increment)
- key (varchar(100), unique)
- value (text)
- type (enum: ['string', 'integer', 'decimal', 'boolean', 'json'])
- category (varchar(50))
- description (text, nullable)
- editable (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
```

### 17. notifications
```
- id (bigint, PK, auto_increment)
- customer_id (bigint, FK, nullable)
- user_id (bigint, FK, nullable)
- type (enum: ['email', 'sms', 'system'])
- subject (varchar(255))
- content (text)
- status (enum: ['pending', 'sent', 'failed'])
- sent_at (timestamp, nullable)
- error_message (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 18. report_schedules
```
- id (bigint, PK, auto_increment)
- report_name (varchar(100))
- report_type (varchar(50))
- parameters (json)
- frequency (enum: ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])
- scheduled_day (int, nullable)
- scheduled_time (time)
- email_recipients (json)
- format (enum: ['pdf', 'excel', 'csv'])
- last_run_at (timestamp, nullable)
- next_run_at (timestamp)
- active (boolean, default: true)
- created_by (bigint, FK)
- created_at (timestamp)
- updated_at (timestamp)
```

### 19. daily_balances
```
- id (bigint, PK, auto_increment)
- account_id (bigint, FK)
- balance_date (date)
- opening_balance (decimal(15,2))
- closing_balance (decimal(15,2))
- minimum_balance (decimal(15,2))
- maximum_balance (decimal(15,2))
- total_credits (decimal(15,2))
- total_debits (decimal(15,2))
- transaction_count (int)
- created_at (timestamp)
- UNIQUE KEY (account_id, balance_date)
```

### 20. login_history
```
- id (bigint, PK, auto_increment)
- user_id (bigint, FK)
- login_at (timestamp)
- logout_at (timestamp, nullable)
- ip_address (varchar(45))
- user_agent (varchar(500))
- session_id (varchar(100))
- login_status (enum: ['success', 'failed'])
- failure_reason (varchar(255), nullable)
- created_at (timestamp)
```

## Indexes

### Primary Indexes
- All `id` columns are primary keys with auto_increment

### Foreign Key Indexes
- All foreign key columns have indexes

### Composite Indexes
```
- accounts: INDEX(customer_id, status)
- transactions: INDEX(account_id, transaction_date)
- transactions: INDEX(transaction_type, status)
- daily_balances: UNIQUE(account_id, balance_date)
- joint_account_holders: INDEX(account_id, customer_id)
- audit_logs: INDEX(auditable_type, auditable_id)
- interest_calculations: INDEX(account_id, calculation_month)
```

### Search Optimization Indexes
```
- customers: INDEX(nic)
- customers: INDEX(mobile_number)
- accounts: INDEX(account_number)
- transactions: INDEX(reference_number)
- transactions: INDEX(transaction_date, status)
- fixed_deposits: INDEX(maturity_date, status)
```

## Relationships Summary

### One-to-Many
- users → audit_logs
- branches → users
- branches → customers
- branches → accounts
- customers → accounts
- customers → customer_documents
- accounts → transactions
- accounts → fixed_deposits
- accounts → daily_balances
- accounts → interest_calculations
- roles → users

### Many-to-Many
- accounts ↔ customers (through joint_account_holders)
- roles ↔ permissions (through role_permissions)

### Self-Referential
- transactions.related_transaction_id → transactions.id
- users.manager_id → users.id

## Database Constraints

### Check Constraints
- accounts.current_balance >= 0
- accounts.available_balance <= current_balance
- fixed_deposits.term_months IN (6, 12, 36)
- interest_rates.rate BETWEEN 0 AND 100
- transactions.amount > 0

### Unique Constraints
- customers.nic
- accounts.account_number
- transactions.reference_number
- fixed_deposits.fd_number
- users.username
- users.email

### Default Values
- accounts.current_balance = 0.00
- accounts.is_joint = false
- users.active = true
- users.login_attempts = 0