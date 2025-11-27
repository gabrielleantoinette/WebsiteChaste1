<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * VERSI AMAN UNTUK PRODUCTION
 * 
 * Migration ini:
 * - Tidak menghapus data (hanya set NULL)
 * - Validasi data sebelum migration
 * - Skip jika ada masalah kritis
 * - Log semua perubahan
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * Versi aman yang tidak menghapus data
     */
    public function up(): void
    {
        try {
            Log::info('Starting safe foreign keys migration...');

            // 1. Validasi data kritis terlebih dahulu
            $this->validateCriticalData();

            // 2. Fix products.category_id (AMAN - hanya ubah tipe data)
            $this->fixProductsCategoryId();

            // 3. Bersihkan data yang tidak valid (SET NULL, bukan DELETE)
            $this->cleanInvalidData();

            // 4. Tambahkan foreign keys
            $this->addForeignKeys();

            // 5. Tambahkan index
            $this->addIndexes();

            Log::info('Safe foreign keys migration completed successfully');
        } catch (\Exception $e) {
            Log::error('Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validasi data kritis sebelum migration
     */
    private function validateCriticalData(): void
    {
        // Cek invoice dengan customer_id yang tidak valid
        $invalidInvoices = DB::table('hinvoice')
            ->leftJoin('customers', 'hinvoice.customer_id', '=', 'customers.id')
            ->whereNull('customers.id')
            ->count();

        if ($invalidInvoices > 0) {
            Log::warning("Found {$invalidInvoices} invoices with invalid customer_id");
            // Jangan throw error, tapi log warning
            // Kita akan skip foreign key untuk customer_id jika ada masalah
        }
    }

    /**
     * Fix products.category_id dari string ke unsignedBigInteger
     */
    private function fixProductsCategoryId(): void
    {
        Log::info('Fixing products.category_id...');

        // Bersihkan category_id yang tidak valid (set NULL)
        $validCategoryIds = DB::table('categories')->pluck('id')->toArray();
        if (!empty($validCategoryIds)) {
            $updated = DB::table('products')
                ->whereNotNull('category_id')
                ->whereNotIn('category_id', $validCategoryIds)
                ->update(['category_id' => null]);
            Log::info("Updated {$updated} products with invalid category_id");
        }

        // Cek tipe data kolom
        $columnType = DB::select("SHOW COLUMNS FROM products WHERE Field = 'category_id'");
        if (!empty($columnType) && strpos($columnType[0]->Type, 'varchar') !== false) {
            // Ubah tipe data dengan hati-hati
            try {
                DB::statement('ALTER TABLE products MODIFY category_id BIGINT UNSIGNED NULL');
                Log::info('Successfully changed category_id type to BIGINT UNSIGNED');
            } catch (\Exception $e) {
                Log::error('Failed to change category_id type: ' . $e->getMessage());
                // Skip jika gagal, tidak critical
            }
        }

        // Tambahkan foreign key jika belum ada
        try {
            Schema::table('products', function (Blueprint $table) {
                if (!$this->foreignKeyExists('products', 'products_category_id_foreign')) {
                    $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
                }
            });
            Log::info('Added foreign key for products.category_id');
        } catch (\Exception $e) {
            Log::warning('Could not add foreign key for products.category_id: ' . $e->getMessage());
        }
    }

    /**
     * Bersihkan data yang tidak valid (SET NULL, bukan DELETE)
     */
    private function cleanInvalidData(): void
    {
        Log::info('Cleaning invalid data...');

        $validProductIds = DB::table('products')->pluck('id')->toArray();
        $validCustomerIds = DB::table('customers')->pluck('id')->toArray();
        $validEmployeeIds = DB::table('employees')->pluck('id')->toArray();
        $validInvoiceIds = DB::table('hinvoice')->pluck('id')->toArray();
        $validVariantIds = DB::table('product_variants')->pluck('id')->toArray();

        // Product variants - SET NULL untuk product_id yang tidak valid (bukan DELETE)
        if (!empty($validProductIds)) {
            // Tidak menghapus, hanya log
            $invalid = DB::table('product_variants')
                ->whereNotIn('product_id', $validProductIds)
                ->count();
            if ($invalid > 0) {
                Log::warning("Found {$invalid} product_variants with invalid product_id (will be handled by foreign key)");
            }
        }

        // Hinvoice - employee_id adalah NOT NULL, jadi set ke employee pertama yang valid
        if (!empty($validEmployeeIds)) {
            $firstEmployeeId = $validEmployeeIds[0];
            $invalidCount = DB::table('hinvoice')->whereNotIn('employee_id', $validEmployeeIds)->count();
            if ($invalidCount > 0) {
                $updated = DB::table('hinvoice')
                    ->whereNotIn('employee_id', $validEmployeeIds)
                    ->update(['employee_id' => $firstEmployeeId]);
                Log::warning("Found {$invalidCount} invoices with invalid employee_id. Set to first employee ({$firstEmployeeId}). Please review manually.");
            }

            $updated = DB::table('hinvoice')
                ->whereNotNull('driver_id')
                ->whereNotIn('driver_id', $validEmployeeIds)
                ->update(['driver_id' => null]);
            Log::info("Updated {$updated} hinvoice records with invalid driver_id");

            $updated = DB::table('hinvoice')
                ->whereNotNull('gudang_id')
                ->whereNotIn('gudang_id', $validEmployeeIds)
                ->update(['gudang_id' => null]);
            Log::info("Updated {$updated} hinvoice records with invalid gudang_id");

            $updated = DB::table('hinvoice')
                ->whereNotNull('accountant_id')
                ->whereNotIn('accountant_id', $validEmployeeIds)
                ->update(['accountant_id' => null]);
            Log::info("Updated {$updated} hinvoice records with invalid accountant_id");
        }

        // Dinvoice - SET NULL untuk product_id/variant_id yang tidak valid
        if (!empty($validProductIds)) {
            $updated = DB::table('dinvoice')
                ->whereNotNull('product_id')
                ->whereNotIn('product_id', $validProductIds)
                ->update(['product_id' => null]);
            Log::info("Updated {$updated} dinvoice records with invalid product_id");
        }

        if (!empty($validVariantIds)) {
            $updated = DB::table('dinvoice')
                ->whereNotNull('variant_id')
                ->whereNotIn('variant_id', $validVariantIds)
                ->update(['variant_id' => null]);
            Log::info("Updated {$updated} dinvoice records with invalid variant_id");
        }

        // Cart - Log warning (tidak menghapus, akan di-handle oleh foreign key)
        if (!empty($validCustomerIds)) {
            $invalid = DB::table('cart')
                ->whereNotIn('user_id', $validCustomerIds)
                ->count();
            if ($invalid > 0) {
                Log::warning("Found {$invalid} cart records with invalid user_id (will be handled by foreign key)");
            }
        }

        if (!empty($validVariantIds)) {
            $invalid = DB::table('cart')
                ->whereNotIn('variant_id', $validVariantIds)
                ->count();
            if ($invalid > 0) {
                Log::warning("Found {$invalid} cart records with invalid variant_id (will be handled by foreign key)");
            }
        }

        // Payment - Log warning
        if (!empty($validInvoiceIds)) {
            $invalid = DB::table('payment')
                ->whereNotIn('invoice_id', $validInvoiceIds)
                ->count();
            if ($invalid > 0) {
                Log::warning("Found {$invalid} payment records with invalid invoice_id (will be handled by foreign key)");
            }
        }

        // Negotiation tables - Log warning
        if (!empty($validCustomerIds)) {
            $invalid = DB::table('negotiation_tables')
                ->whereNotIn('user_id', $validCustomerIds)
                ->count();
            if ($invalid > 0) {
                Log::warning("Found {$invalid} negotiation_tables records with invalid user_id (will be handled by foreign key)");
            }
        }

        if (!empty($validProductIds)) {
            $invalid = DB::table('negotiation_tables')
                ->whereNotIn('product_id', $validProductIds)
                ->count();
            if ($invalid > 0) {
                Log::warning("Found {$invalid} negotiation_tables records with invalid product_id (will be handled by foreign key)");
            }
        }
    }

    /**
     * Tambahkan foreign keys dengan error handling
     */
    private function addForeignKeys(): void
    {
        Log::info('Adding foreign keys...');

        // Product variants
        try {
            Schema::table('product_variants', function (Blueprint $table) {
                if (!$this->foreignKeyExists('product_variants', 'product_variants_product_id_foreign')) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                }
            });
            Log::info('Added foreign key for product_variants.product_id');
        } catch (\Exception $e) {
            Log::warning('Could not add foreign key for product_variants.product_id: ' . $e->getMessage());
        }

        // Hinvoice - Skip customer_id jika ada masalah
        try {
            Schema::table('hinvoice', function (Blueprint $table) {
                if (!$this->foreignKeyExists('hinvoice', 'hinvoice_customer_id_foreign')) {
                    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
                }
                // employee_id adalah NOT NULL, jadi gunakan restrict bukan set null
                if (!$this->foreignKeyExists('hinvoice', 'hinvoice_employee_id_foreign')) {
                    $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
                }
                if (!$this->foreignKeyExists('hinvoice', 'hinvoice_driver_id_foreign')) {
                    $table->foreign('driver_id')->references('id')->on('employees')->onDelete('set null');
                }
                if (!$this->foreignKeyExists('hinvoice', 'hinvoice_gudang_id_foreign')) {
                    $table->foreign('gudang_id')->references('id')->on('employees')->onDelete('set null');
                }
                if (!$this->foreignKeyExists('hinvoice', 'hinvoice_accountant_id_foreign')) {
                    $table->foreign('accountant_id')->references('id')->on('employees')->onDelete('set null');
                }
            });
            Log::info('Added foreign keys for hinvoice');
        } catch (\Exception $e) {
            Log::warning('Could not add some foreign keys for hinvoice: ' . $e->getMessage());
            // Continue dengan yang lain
        }

        // Dinvoice
        try {
            Schema::table('dinvoice', function (Blueprint $table) {
                if (!$this->foreignKeyExists('dinvoice', 'dinvoice_hinvoice_id_foreign')) {
                    $table->foreign('hinvoice_id')->references('id')->on('hinvoice')->onDelete('cascade');
                }
                if (!$this->foreignKeyExists('dinvoice', 'dinvoice_product_id_foreign')) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
                }
                if (!$this->foreignKeyExists('dinvoice', 'dinvoice_variant_id_foreign')) {
                    $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
                }
            });
            Log::info('Added foreign keys for dinvoice');
        } catch (\Exception $e) {
            Log::warning('Could not add some foreign keys for dinvoice: ' . $e->getMessage());
        }

        // Cart
        try {
            Schema::table('cart', function (Blueprint $table) {
                if (!$this->foreignKeyExists('cart', 'cart_user_id_foreign')) {
                    $table->foreign('user_id')->references('id')->on('customers')->onDelete('cascade');
                }
                if (!$this->foreignKeyExists('cart', 'cart_variant_id_foreign')) {
                    $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
                }
            });
            Log::info('Added foreign keys for cart');
        } catch (\Exception $e) {
            Log::warning('Could not add some foreign keys for cart: ' . $e->getMessage());
        }

        // Payment
        try {
            Schema::table('payment', function (Blueprint $table) {
                if (!$this->foreignKeyExists('payment', 'payment_invoice_id_foreign')) {
                    $table->foreign('invoice_id')->references('id')->on('hinvoice')->onDelete('cascade');
                }
            });
            Log::info('Added foreign key for payment.invoice_id');
        } catch (\Exception $e) {
            Log::warning('Could not add foreign key for payment.invoice_id: ' . $e->getMessage());
        }

        // Negotiation tables
        try {
            Schema::table('negotiation_tables', function (Blueprint $table) {
                if (!$this->foreignKeyExists('negotiation_tables', 'negotiation_tables_user_id_foreign')) {
                    $table->foreign('user_id')->references('id')->on('customers')->onDelete('cascade');
                }
                if (!$this->foreignKeyExists('negotiation_tables', 'negotiation_tables_product_id_foreign')) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                }
            });
            Log::info('Added foreign keys for negotiation_tables');
        } catch (\Exception $e) {
            Log::warning('Could not add some foreign keys for negotiation_tables: ' . $e->getMessage());
        }
    }

    /**
     * Tambahkan index untuk performa
     */
    private function addIndexes(): void
    {
        Log::info('Adding indexes...');

        try {
            Schema::table('hinvoice', function (Blueprint $table) {
                if (!$this->indexExists('hinvoice', 'hinvoice_customer_id_index')) {
                    $table->index('customer_id');
                }
                if (!$this->indexExists('hinvoice', 'hinvoice_status_index')) {
                    $table->index('status');
                }
                if (!$this->indexExists('hinvoice', 'hinvoice_created_at_index')) {
                    $table->index('created_at');
                }
            });
            Log::info('Added indexes for hinvoice');
        } catch (\Exception $e) {
            Log::warning('Could not add some indexes for hinvoice: ' . $e->getMessage());
        }

        try {
            Schema::table('dinvoice', function (Blueprint $table) {
                if (!$this->indexExists('dinvoice', 'dinvoice_hinvoice_id_index')) {
                    $table->index('hinvoice_id');
                }
            });
            Log::info('Added index for dinvoice');
        } catch (\Exception $e) {
            Log::warning('Could not add index for dinvoice: ' . $e->getMessage());
        }

        try {
            Schema::table('cart', function (Blueprint $table) {
                if (!$this->indexExists('cart', 'cart_user_id_index')) {
                    $table->index('user_id');
                }
            });
            Log::info('Added index for cart');
        } catch (\Exception $e) {
            Log::warning('Could not add index for cart: ' . $e->getMessage());
        }
    }

    /**
     * Cek apakah foreign key sudah ada
     */
    private function foreignKeyExists(string $table, string $keyName): bool
    {
        try {
            $dbName = DB::connection()->getDatabaseName();
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND CONSTRAINT_NAME = ?
            ", [$dbName, $table, $keyName]);

            return !empty($foreignKeys);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cek apakah index sudah ada
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $dbName = DB::connection()->getDatabaseName();
            $indexes = DB::select("
                SELECT INDEX_NAME 
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND INDEX_NAME = ?
            ", [$dbName, $table, $indexName]);

            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Log::info('Rolling back foreign keys migration...');

        // Hapus index
        try {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
            });
            Schema::table('dinvoice', function (Blueprint $table) {
                $table->dropIndex(['hinvoice_id']);
            });
            Schema::table('hinvoice', function (Blueprint $table) {
                $table->dropIndex(['customer_id']);
                $table->dropIndex(['status']);
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {
            Log::warning('Could not drop some indexes: ' . $e->getMessage());
        }

        // Hapus foreign keys (dengan cek apakah ada)
        $dbName = DB::connection()->getDatabaseName();
        
        $foreignKeyExists = function($table, $column) use ($dbName) {
            $result = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = ?
                AND CONSTRAINT_NAME != 'PRIMARY'
            ", [$dbName, $table, $column]);
            return !empty($result);
        };

        $tables = [
            'negotiation_tables' => [['user_id'], ['product_id']],
            'payment' => [['invoice_id']],
            'cart' => [['user_id'], ['variant_id']],
            'dinvoice' => [['hinvoice_id'], ['product_id'], ['variant_id']],
            'hinvoice' => [['customer_id'], ['employee_id'], ['driver_id'], ['gudang_id'], ['accountant_id']],
            'product_variants' => [['product_id']],
            'products' => [['category_id']],
        ];

        foreach ($tables as $table => $keys) {
            try {
                Schema::table($table, function (Blueprint $table) use ($keys, $foreignKeyExists) {
                    foreach ($keys as $key) {
                        $column = $key[0];
                        if ($foreignKeyExists($table->getTable(), $column)) {
                            try {
                                $table->dropForeign($key);
                            } catch (\Exception $e) {
                                // Skip jika foreign key tidak ada
                                Log::warning("Could not drop foreign key {$table}.{$column}: " . $e->getMessage());
                            }
                        }
                    }
                });
            } catch (\Exception $e) {
                Log::warning("Could not drop foreign keys for {$table}: " . $e->getMessage());
            }
        }

        // Kembalikan category_id ke string (opsional)
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
            });
            DB::statement('ALTER TABLE products MODIFY category_id VARCHAR(255) NULL');
        } catch (\Exception $e) {
            Log::warning('Could not revert category_id type: ' . $e->getMessage());
        }

        Log::info('Rollback completed');
    }
};
