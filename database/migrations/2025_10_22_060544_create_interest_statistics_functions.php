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

        if ($driver === 'pgsql') {
            // PostgreSQL: Create function for interest distribution by customer status
            DB::unprepared("
                CREATE OR REPLACE FUNCTION get_interest_by_customer_status()
                RETURNS TABLE(status_name VARCHAR, total_interest DECIMAL(15,2))
                LANGUAGE plpgsql
                AS $$
                BEGIN
                    RETURN QUERY
                    SELECT
                        status.status_name::VARCHAR,
                        SUM(calc.interest_amount)::DECIMAL(15,2) as total_interest
                    FROM savings_account_interest_calculations calc
                    JOIN savings_account acc ON calc.account_id = acc.id
                    JOIN savings_account_type type ON acc.account_type_id = type.id
                    JOIN customer_status_types status ON type.customer_status_id = status.id
                    WHERE calc.status = 'CREDITED'
                    GROUP BY status.status_name;
                END;
                $$;
            ");

            // PostgreSQL: Create function for average interest rates by customer status
            DB::unprepared("
                CREATE OR REPLACE FUNCTION get_avg_interest_rates_by_status()
                RETURNS TABLE(status_name VARCHAR, avg_rate DECIMAL(5,4))
                LANGUAGE plpgsql
                AS $$
                BEGIN
                    RETURN QUERY
                    SELECT
                        status.status_name::VARCHAR,
                        AVG(type.interest_rate)::DECIMAL(5,4) as avg_rate
                    FROM savings_account_type type
                    JOIN customer_status_types status ON type.customer_status_id = status.id
                    GROUP BY status.status_name;
                END;
                $$;
            ");

            // PostgreSQL: Create function for top accounts by interest earned
            DB::unprepared("
                CREATE OR REPLACE FUNCTION get_top_accounts_by_interest(limit_count INTEGER DEFAULT 5)
                RETURNS TABLE(account_number VARCHAR, total_interest DECIMAL(15,2))
                LANGUAGE plpgsql
                AS $$
                BEGIN
                    RETURN QUERY
                    SELECT
                        acc.account_number::VARCHAR,
                        SUM(calc.interest_amount)::DECIMAL(15,2) as total_interest
                    FROM savings_account_interest_calculations calc
                    JOIN savings_account acc ON calc.account_id = acc.id
                    WHERE calc.status = 'CREDITED'
                    GROUP BY acc.id, acc.account_number
                    ORDER BY total_interest DESC
                    LIMIT limit_count;
                END;
                $$;
            ");

            // PostgreSQL: Create function for interest by account type
            DB::unprepared("
                CREATE OR REPLACE FUNCTION get_interest_by_account_type()
                RETURNS TABLE(type_name VARCHAR, total_interest DECIMAL(15,2))
                LANGUAGE plpgsql
                AS $$
                BEGIN
                    RETURN QUERY
                    SELECT
                        type.name::VARCHAR,
                        SUM(calc.interest_amount)::DECIMAL(15,2) as total_interest
                    FROM savings_account_interest_calculations calc
                    JOIN savings_account acc ON calc.account_id = acc.id
                    JOIN savings_account_type type ON acc.account_type_id = type.id
                    WHERE calc.status = 'CREDITED'
                    GROUP BY type.name;
                END;
                $$;
            ");

            // PostgreSQL: Create function for monthly interest trends
            DB::unprepared("
                CREATE OR REPLACE FUNCTION get_monthly_interest_trends(months_back INTEGER DEFAULT 12)
                RETURNS TABLE(month_year VARCHAR, total_interest DECIMAL(15,2))
                LANGUAGE plpgsql
                AS $$
                BEGIN
                    RETURN QUERY
                    SELECT
                        TO_CHAR(calc.credited_date, 'YYYY-MM')::VARCHAR as month_year,
                        SUM(calc.interest_amount)::DECIMAL(15,2) as total_interest
                    FROM savings_account_interest_calculations calc
                    WHERE calc.status = 'CREDITED'
                        AND calc.credited_date >= CURRENT_DATE - (months_back || ' months')::INTERVAL
                        AND calc.credited_date IS NOT NULL
                    GROUP BY TO_CHAR(calc.credited_date, 'YYYY-MM')
                    ORDER BY month_year;
                END;
                $$;
            ");
        } else {
            // SQLite: Create views as SQLite doesn't support stored functions
            // View for interest distribution by customer status
            DB::unprepared("
                CREATE VIEW IF NOT EXISTS vw_interest_by_customer_status AS
                SELECT
                    status.status_name,
                    SUM(calc.interest_amount) as total_interest
                FROM savings_account_interest_calculations calc
                JOIN savings_account acc ON calc.account_id = acc.id
                JOIN savings_account_type type ON acc.account_type_id = type.id
                JOIN customer_status_types status ON type.customer_status_id = status.id
                WHERE calc.status = 'CREDITED'
                GROUP BY status.status_name;
            ");

            // View for average interest rates by customer status
            DB::unprepared("
                CREATE VIEW IF NOT EXISTS vw_avg_interest_rates_by_status AS
                SELECT
                    status.status_name,
                    AVG(type.interest_rate) as avg_rate
                FROM savings_account_type type
                JOIN customer_status_types status ON type.customer_status_id = status.id
                GROUP BY status.status_name;
            ");

            // View for top accounts by interest earned (top 5)
            DB::unprepared("
                CREATE VIEW IF NOT EXISTS vw_top_accounts_by_interest AS
                SELECT
                    acc.account_number,
                    SUM(calc.interest_amount) as total_interest
                FROM savings_account_interest_calculations calc
                JOIN savings_account acc ON calc.account_id = acc.id
                WHERE calc.status = 'CREDITED'
                GROUP BY acc.id, acc.account_number
                ORDER BY total_interest DESC
                LIMIT 5;
            ");

            // View for interest by account type
            DB::unprepared("
                CREATE VIEW IF NOT EXISTS vw_interest_by_account_type AS
                SELECT
                    type.name as type_name,
                    SUM(calc.interest_amount) as total_interest
                FROM savings_account_interest_calculations calc
                JOIN savings_account acc ON calc.account_id = acc.id
                JOIN savings_account_type type ON acc.account_type_id = type.id
                WHERE calc.status = 'CREDITED'
                GROUP BY type.name;
            ");

            // View for monthly interest trends (last 12 months)
            DB::unprepared("
                CREATE VIEW IF NOT EXISTS vw_monthly_interest_trends AS
                SELECT
                    strftime('%Y-%m', calc.credited_date) as month_year,
                    SUM(calc.interest_amount) as total_interest
                FROM savings_account_interest_calculations calc
                WHERE calc.status = 'CREDITED'
                    AND calc.credited_date >= date('now', '-12 months')
                    AND calc.credited_date IS NOT NULL
                GROUP BY strftime('%Y-%m', calc.credited_date)
                ORDER BY month_year;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::unprepared('DROP FUNCTION IF EXISTS get_interest_by_customer_status()');
            DB::unprepared('DROP FUNCTION IF EXISTS get_avg_interest_rates_by_status()');
            DB::unprepared('DROP FUNCTION IF EXISTS get_top_accounts_by_interest(INTEGER)');
            DB::unprepared('DROP FUNCTION IF EXISTS get_interest_by_account_type()');
            DB::unprepared('DROP FUNCTION IF EXISTS get_monthly_interest_trends(INTEGER)');
        } else {
            DB::unprepared('DROP VIEW IF EXISTS vw_interest_by_customer_status');
            DB::unprepared('DROP VIEW IF EXISTS vw_avg_interest_rates_by_status');
            DB::unprepared('DROP VIEW IF EXISTS vw_top_accounts_by_interest');
            DB::unprepared('DROP VIEW IF EXISTS vw_interest_by_account_type');
            DB::unprepared('DROP VIEW IF EXISTS vw_monthly_interest_trends');
        }
    }
};
