<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite does not support stored functions in the same way; create a user-defined function in PHP for tests if needed
            // No-op for migration
        } elseif ($driver === 'pgsql') {
            // PostgreSQL function
            DB::unprepared('
                CREATE OR REPLACE FUNCTION can_withdraw(
                    acc_num VARCHAR,
                    withdraw_amount NUMERIC
                ) RETURNS BOOLEAN AS $$
                DECLARE
                    acc_id BIGINT;
                    current_balance NUMERIC;
                    min_balance NUMERIC;
                    withdrawals_this_month INT;
                    max_withdrawals INT;
                    allowed BOOLEAN := TRUE;
                BEGIN
                    SELECT sa.id, sa.balance, sat.min_balance, sat.max_withdrawals_per_month
                    INTO acc_id, current_balance, min_balance, max_withdrawals
                    FROM savings_account sa
                    JOIN savings_account_type sat ON sa.account_type_id = sat.id
                    WHERE sa.account_number = acc_num;

                    IF current_balance - withdraw_amount < min_balance THEN
                        allowed := FALSE;
                    END IF;

                    SELECT COUNT(*) INTO withdrawals_this_month
                    FROM savings_transaction
                    WHERE from_id = acc_id
                    AND type = \'WITHDRAWAL\'
                    AND EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE);

                    IF max_withdrawals IS NOT NULL AND withdrawals_this_month >= max_withdrawals THEN
                        allowed := FALSE;
                    END IF;

                    RETURN allowed;
                END;
                $$ LANGUAGE plpgsql;
            ');
        } else {
            // MySQL/MariaDB function
            DB::unprepared('
                CREATE FUNCTION can_withdraw(
                    acc_num VARCHAR(20),
                    withdraw_amount DECIMAL(15,2)
                ) RETURNS BOOLEAN
                DETERMINISTIC
                BEGIN
                    DECLARE acc_id BIGINT;
                    DECLARE current_balance DECIMAL(15,2);
                    DECLARE min_balance DECIMAL(15,2);
                    DECLARE withdrawals_this_month INT;
                    DECLARE max_withdrawals INT;
                    DECLARE allowed BOOLEAN DEFAULT 1;

                    SELECT sa.id, sa.balance, sat.min_balance, sat.max_withdrawals_per_month
                    INTO acc_id, current_balance, min_balance, max_withdrawals
                    FROM savings_account sa
                    JOIN savings_account_type sat ON sa.account_type_id = sat.id
                    WHERE sa.account_number = acc_num;

                    IF current_balance - withdraw_amount < min_balance THEN
                        SET allowed = 0;
                    END IF;

                    SELECT COUNT(*) INTO withdrawals_this_month
                    FROM savings_transaction
                    WHERE from_id = acc_id
                    AND type = \'WITHDRAWAL\'
                    AND MONTH(created_at) = MONTH(CURRENT_DATE())
                    AND YEAR(created_at) = YEAR(CURRENT_DATE());

                    IF max_withdrawals IS NOT NULL AND withdrawals_this_month >= max_withdrawals THEN
                        SET allowed = 0;
                    END IF;

                    RETURN allowed;
                END
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // No-op
        } elseif ($driver === 'pgsql') {
            DB::unprepared('DROP FUNCTION IF EXISTS can_withdraw(VARCHAR, NUMERIC);');
        } else {
            DB::unprepared('DROP FUNCTION IF EXISTS can_withdraw;');
        }
    }
};