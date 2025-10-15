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
            // SQLite trigger for credit (to_id)
            DB::unprepared('
                CREATE TRIGGER update_account_balance_credit
                AFTER INSERT ON savings_transaction
                WHEN NEW.to_id IS NOT NULL
                BEGIN
                    UPDATE savings_account
                    SET balance = balance + NEW.amount,
                        last_transaction_date = NEW.created_at,
                        updated_at = datetime("now")
                    WHERE id = NEW.to_id;
                END
            ');

            // SQLite trigger for debit (from_id)
            DB::unprepared('
                CREATE TRIGGER update_account_balance_debit
                AFTER INSERT ON savings_transaction
                WHEN NEW.from_id IS NOT NULL
                BEGIN
                    UPDATE savings_account
                    SET balance = balance - NEW.amount,
                        last_transaction_date = NEW.created_at,
                        updated_at = datetime("now")
                    WHERE id = NEW.from_id;
                END
            ');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL trigger function
            DB::unprepared('
                CREATE OR REPLACE FUNCTION update_account_balance_on_transaction()
                RETURNS TRIGGER AS $$
                BEGIN
                    -- Update the TO account (credit)
                    IF NEW.to_id IS NOT NULL THEN
                        UPDATE savings_account
                        SET balance = balance + NEW.amount,
                            last_transaction_date = NEW.created_at,
                            updated_at = NOW()
                        WHERE id = NEW.to_id;
                    END IF;

                    -- Update the FROM account (debit)
                    IF NEW.from_id IS NOT NULL THEN
                        UPDATE savings_account
                        SET balance = balance - NEW.amount,
                            last_transaction_date = NEW.created_at,
                            updated_at = NOW()
                        WHERE id = NEW.from_id;
                    END IF;

                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            ');

            // Create trigger that calls the function
            DB::unprepared('
                CREATE TRIGGER update_account_balance_after_insert
                AFTER INSERT ON savings_transaction
                FOR EACH ROW
                EXECUTE FUNCTION update_account_balance_on_transaction();
            ');
        } else {
            // MySQL/MariaDB trigger
            DB::unprepared('
                CREATE TRIGGER update_account_balance_after_insert
                AFTER INSERT ON savings_transaction
                FOR EACH ROW
                BEGIN
                    IF NEW.to_id IS NOT NULL THEN
                        UPDATE savings_account
                        SET balance = balance + NEW.amount,
                            last_transaction_date = NEW.created_at,
                            updated_at = NOW()
                        WHERE id = NEW.to_id;
                    END IF;

                    IF NEW.from_id IS NOT NULL THEN
                        UPDATE savings_account
                        SET balance = balance - NEW.amount,
                            last_transaction_date = NEW.created_at,
                            updated_at = NOW()
                        WHERE id = NEW.from_id;
                    END IF;
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
            DB::unprepared('DROP TRIGGER IF EXISTS update_account_balance_credit');
            DB::unprepared('DROP TRIGGER IF EXISTS update_account_balance_debit');
        } elseif ($driver === 'pgsql') {
            DB::unprepared('DROP TRIGGER IF EXISTS update_account_balance_after_insert ON savings_transaction');
            DB::unprepared('DROP FUNCTION IF EXISTS update_account_balance_on_transaction()');
        } else {
            DB::unprepared('DROP TRIGGER IF EXISTS update_account_balance_after_insert');
        }
    }
};
