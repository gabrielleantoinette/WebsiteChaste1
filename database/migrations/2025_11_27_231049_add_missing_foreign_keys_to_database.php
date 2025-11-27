<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan foreign key constraints yang hilang untuk menjaga referential integrity
     */
    public function up(): void
    {
        // 1. Fix products.category_id dari string ke unsignedBigInteger + foreign key
        // Pertama, bersihkan data yang tidak valid (category_id yang tidak ada di tabel categories)
        $validCategoryIds = DB::table('categories')->pluck('id')->toArray();
        if (!empty($validCategoryIds)) {
            DB::table('products')
                ->whereNotNull('category_id')
                ->whereNotIn('category_id', $validCategoryIds)
                ->update(['category_id' => null]);
        } else {
            // Jika tidak ada kategori, set semua ke null
            DB::table('products')->whereNotNull('category_id')->update(['category_id' => null]);
        }
        
        // Ubah tipe data category_id dari string ke unsignedBigInteger
        // Cek dulu apakah kolom sudah integer atau masih string
        $columnType = DB::select("SHOW COLUMNS FROM products WHERE Field = 'category_id'");
        if (!empty($columnType) && strpos($columnType[0]->Type, 'varchar') !== false) {
            DB::statement('ALTER TABLE products MODIFY category_id BIGINT UNSIGNED NULL');
        }
        
        // Tambahkan foreign key (cek dulu apakah sudah ada)
        $dbName = DB::connection()->getDatabaseName();
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key mungkin sudah ada, skip
            if (strpos($e->getMessage(), 'Duplicate key') === false) {
                throw $e;
            }
        }

        // 2. Bersihkan data yang tidak valid sebelum menambahkan foreign key
        // Bersihkan product_variants dengan product_id yang tidak ada
        $validProductIds = DB::table('products')->pluck('id')->toArray();
        if (!empty($validProductIds)) {
            DB::table('product_variants')->whereNotIn('product_id', $validProductIds)->delete();
        } else {
            // Jangan truncate jika ada foreign key constraint, gunakan delete
            DB::table('product_variants')->delete();
        }
        
        // Bersihkan hinvoice dengan customer_id atau employee_id yang tidak ada
        $validCustomerIds = DB::table('customers')->pluck('id')->toArray();
        $validEmployeeIds = DB::table('employees')->pluck('id')->toArray();
        
        // Customer_id adalah required, jadi kita tidak bisa set ke null
        // Jika ada invoice dengan customer_id yang tidak valid, migration akan gagal
        // Ini adalah expected behavior untuk menjaga data integrity
        
        if (!empty($validEmployeeIds)) {
            // employee_id adalah NOT NULL, jadi kita tidak bisa set null
            // Set ke employee pertama yang ada jika tidak valid
            $firstEmployeeId = $validEmployeeIds[0];
            $invalidCount = DB::table('hinvoice')->whereNotIn('employee_id', $validEmployeeIds)->count();
            if ($invalidCount > 0) {
                // Set ke employee pertama yang valid
                DB::table('hinvoice')->whereNotIn('employee_id', $validEmployeeIds)->update(['employee_id' => $firstEmployeeId]);
            }
            DB::table('hinvoice')
                ->whereNotNull('driver_id')
                ->whereNotIn('driver_id', $validEmployeeIds)
                ->update(['driver_id' => null]);
            DB::table('hinvoice')
                ->whereNotNull('gudang_id')
                ->whereNotIn('gudang_id', $validEmployeeIds)
                ->update(['gudang_id' => null]);
            DB::table('hinvoice')
                ->whereNotNull('accountant_id')
                ->whereNotIn('accountant_id', $validEmployeeIds)
                ->update(['accountant_id' => null]);
        }
        
        // Bersihkan dinvoice dengan hinvoice_id, product_id, atau variant_id yang tidak ada
        $validInvoiceIds = DB::table('hinvoice')->pluck('id')->toArray();
        $validVariantIds = DB::table('product_variants')->pluck('id')->toArray();
        
        if (!empty($validInvoiceIds)) {
            DB::table('dinvoice')->whereNotIn('hinvoice_id', $validInvoiceIds)->delete();
        } else {
            DB::table('dinvoice')->truncate();
        }
        
        if (!empty($validProductIds)) {
            DB::table('dinvoice')
                ->whereNotNull('product_id')
                ->whereNotIn('product_id', $validProductIds)
                ->update(['product_id' => null]);
        }
        
        if (!empty($validVariantIds)) {
            DB::table('dinvoice')
                ->whereNotNull('variant_id')
                ->whereNotIn('variant_id', $validVariantIds)
                ->update(['variant_id' => null]);
        }
        
        // Bersihkan cart dengan user_id atau variant_id yang tidak ada
        $validCustomerIds = DB::table('customers')->pluck('id')->toArray();
        if (!empty($validCustomerIds)) {
            DB::table('cart')->whereNotIn('user_id', $validCustomerIds)->delete();
        }
        
        if (!empty($validVariantIds)) {
            DB::table('cart')->whereNotIn('variant_id', $validVariantIds)->delete();
        }
        
        // Bersihkan payment dengan invoice_id yang tidak ada
        if (!empty($validInvoiceIds)) {
            DB::table('payment')->whereNotIn('invoice_id', $validInvoiceIds)->delete();
        }
        
        // Bersihkan negotiation_tables dengan user_id atau product_id yang tidak ada
        if (!empty($validCustomerIds)) {
            DB::table('negotiation_tables')->whereNotIn('user_id', $validCustomerIds)->delete();
        }
        
        if (!empty($validProductIds)) {
            DB::table('negotiation_tables')->whereNotIn('product_id', $validProductIds)->delete();
        }

        // 3. Tambahkan foreign key untuk product_variants (cek dulu)
        $existingFK = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'product_variants' 
            AND CONSTRAINT_NAME = 'product_variants_product_id_foreign'
        ", [$dbName]);
        
        if (empty($existingFK)) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        // 4. Tambahkan foreign key untuk hinvoice (cek dulu)
        $fkNames = ['hinvoice_customer_id_foreign', 'hinvoice_employee_id_foreign', 'hinvoice_driver_id_foreign', 'hinvoice_gudang_id_foreign', 'hinvoice_accountant_id_foreign'];
        $existingFKs = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'hinvoice' 
            AND CONSTRAINT_NAME IN ('" . implode("','", $fkNames) . "')
        ", [$dbName]);
        $existingFKNames = array_column($existingFKs, 'CONSTRAINT_NAME');
        
        Schema::table('hinvoice', function (Blueprint $table) use ($existingFKNames) {
            if (!in_array('hinvoice_customer_id_foreign', $existingFKNames)) {
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            }
            if (!in_array('hinvoice_employee_id_foreign', $existingFKNames)) {
                // employee_id adalah NOT NULL, jadi gunakan restrict bukan set null
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
            }
            if (!in_array('hinvoice_driver_id_foreign', $existingFKNames)) {
                $table->foreign('driver_id')->references('id')->on('employees')->onDelete('set null');
            }
            if (!in_array('hinvoice_gudang_id_foreign', $existingFKNames)) {
                $table->foreign('gudang_id')->references('id')->on('employees')->onDelete('set null');
            }
            if (!in_array('hinvoice_accountant_id_foreign', $existingFKNames)) {
                $table->foreign('accountant_id')->references('id')->on('employees')->onDelete('set null');
            }
        });

        // 5. Tambahkan foreign key untuk dinvoice (cek dulu)
        $fkNames = ['dinvoice_hinvoice_id_foreign', 'dinvoice_product_id_foreign', 'dinvoice_variant_id_foreign'];
        $existingFKs = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'dinvoice' 
            AND CONSTRAINT_NAME IN ('" . implode("','", $fkNames) . "')
        ", [$dbName]);
        $existingFKNames = array_column($existingFKs, 'CONSTRAINT_NAME');
        
        Schema::table('dinvoice', function (Blueprint $table) use ($existingFKNames) {
            if (!in_array('dinvoice_hinvoice_id_foreign', $existingFKNames)) {
                $table->foreign('hinvoice_id')->references('id')->on('hinvoice')->onDelete('cascade');
            }
            if (!in_array('dinvoice_product_id_foreign', $existingFKNames)) {
                $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            }
            if (!in_array('dinvoice_variant_id_foreign', $existingFKNames)) {
                $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
            }
        });

        // 6. Tambahkan foreign key untuk cart (cek dulu)
        $fkNames = ['cart_user_id_foreign', 'cart_variant_id_foreign'];
        $existingFKs = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'cart' 
            AND CONSTRAINT_NAME IN ('" . implode("','", $fkNames) . "')
        ", [$dbName]);
        $existingFKNames = array_column($existingFKs, 'CONSTRAINT_NAME');
        
        Schema::table('cart', function (Blueprint $table) use ($existingFKNames) {
            if (!in_array('cart_user_id_foreign', $existingFKNames)) {
                $table->foreign('user_id')->references('id')->on('customers')->onDelete('cascade');
            }
            if (!in_array('cart_variant_id_foreign', $existingFKNames)) {
                $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            }
        });

        // 7. Tambahkan foreign key untuk payment (cek dulu)
        $existingFK = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'payment' 
            AND CONSTRAINT_NAME = 'payment_invoice_id_foreign'
        ", [$dbName]);
        
        if (empty($existingFK)) {
            Schema::table('payment', function (Blueprint $table) {
                $table->foreign('invoice_id')->references('id')->on('hinvoice')->onDelete('cascade');
            });
        }

        // 8. Tambahkan foreign key untuk negotiation_tables (cek dulu)
        $fkNames = ['negotiation_tables_user_id_foreign', 'negotiation_tables_product_id_foreign'];
        $existingFKs = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'negotiation_tables' 
            AND CONSTRAINT_NAME IN ('" . implode("','", $fkNames) . "')
        ", [$dbName]);
        $existingFKNames = array_column($existingFKs, 'CONSTRAINT_NAME');
        
        Schema::table('negotiation_tables', function (Blueprint $table) use ($existingFKNames) {
            if (!in_array('negotiation_tables_user_id_foreign', $existingFKNames)) {
                $table->foreign('user_id')->references('id')->on('customers')->onDelete('cascade');
            }
            if (!in_array('negotiation_tables_product_id_foreign', $existingFKNames)) {
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            }
        });

        // 8. Tambahkan index untuk performa
        Schema::table('hinvoice', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('dinvoice', function (Blueprint $table) {
            $table->index('hinvoice_id');
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dbName = DB::connection()->getDatabaseName();
        
        // Helper function untuk cek foreign key
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
        
        // Helper function untuk cek index
        $indexExists = function($table, $indexName) use ($dbName) {
            $result = DB::select("
                SELECT INDEX_NAME 
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND INDEX_NAME = ?
            ", [$dbName, $table, $indexName]);
            return !empty($result);
        };

        // Hapus index terlebih dahulu (dengan cek)
        try {
            if ($indexExists('cart', 'cart_user_id_index')) {
                Schema::table('cart', function (Blueprint $table) {
                    $table->dropIndex(['user_id']);
                });
            }
        } catch (\Exception $e) {}

        try {
            if ($indexExists('dinvoice', 'dinvoice_hinvoice_id_index')) {
                Schema::table('dinvoice', function (Blueprint $table) {
                    $table->dropIndex(['hinvoice_id']);
                });
            }
        } catch (\Exception $e) {}

        try {
            if ($indexExists('hinvoice', 'hinvoice_customer_id_index')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropIndex(['customer_id']);
                });
            }
            if ($indexExists('hinvoice', 'hinvoice_status_index')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropIndex(['status']);
                });
            }
            if ($indexExists('hinvoice', 'hinvoice_created_at_index')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropIndex(['created_at']);
                });
            }
        } catch (\Exception $e) {}

        // Hapus foreign keys (dengan cek)
        try {
            if ($foreignKeyExists('negotiation_tables', 'user_id')) {
                Schema::table('negotiation_tables', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            }
            if ($foreignKeyExists('negotiation_tables', 'product_id')) {
                Schema::table('negotiation_tables', function (Blueprint $table) {
                    $table->dropForeign(['product_id']);
                });
            }
        } catch (\Exception $e) {}

        try {
            if ($foreignKeyExists('payment', 'invoice_id')) {
                Schema::table('payment', function (Blueprint $table) {
                    $table->dropForeign(['invoice_id']);
                });
            }
        } catch (\Exception $e) {}

        try {
            if ($foreignKeyExists('cart', 'user_id')) {
                Schema::table('cart', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            }
            if ($foreignKeyExists('cart', 'variant_id')) {
                Schema::table('cart', function (Blueprint $table) {
                    $table->dropForeign(['variant_id']);
                });
            }
        } catch (\Exception $e) {}

        try {
            if ($foreignKeyExists('dinvoice', 'hinvoice_id')) {
                Schema::table('dinvoice', function (Blueprint $table) {
                    $table->dropForeign(['hinvoice_id']);
                });
            }
            if ($foreignKeyExists('dinvoice', 'product_id')) {
                Schema::table('dinvoice', function (Blueprint $table) {
                    $table->dropForeign(['product_id']);
                });
            }
            if ($foreignKeyExists('dinvoice', 'variant_id')) {
                Schema::table('dinvoice', function (Blueprint $table) {
                    $table->dropForeign(['variant_id']);
                });
            }
        } catch (\Exception $e) {}

        try {
            if ($foreignKeyExists('hinvoice', 'customer_id')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropForeign(['customer_id']);
                });
            }
            if ($foreignKeyExists('hinvoice', 'employee_id')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropForeign(['employee_id']);
                });
            }
            if ($foreignKeyExists('hinvoice', 'driver_id')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropForeign(['driver_id']);
                });
            }
            if ($foreignKeyExists('hinvoice', 'gudang_id')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropForeign(['gudang_id']);
                });
            }
            if ($foreignKeyExists('hinvoice', 'accountant_id')) {
                Schema::table('hinvoice', function (Blueprint $table) {
                    $table->dropForeign(['accountant_id']);
                });
            }
        } catch (\Exception $e) {}

        try {
            if ($foreignKeyExists('product_variants', 'product_id')) {
                Schema::table('product_variants', function (Blueprint $table) {
                    $table->dropForeign(['product_id']);
                });
            }
        } catch (\Exception $e) {}

        // Kembalikan products.category_id ke string
        try {
            if ($foreignKeyExists('products', 'category_id')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropForeign(['category_id']);
                });
            }
        } catch (\Exception $e) {}
        
        try {
            // Kembalikan category_id ke string (cast ke string)
            DB::statement('ALTER TABLE products MODIFY category_id VARCHAR(255) NULL');
        } catch (\Exception $e) {}
    }
};
