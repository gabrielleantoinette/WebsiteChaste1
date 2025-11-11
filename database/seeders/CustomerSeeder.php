<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            'Surabaya', 'Gresik', 'Sidoarjo', 'Malang', 'Kediri',
            'Pasuruan', 'Mojokerto', 'Jember', 'Ngawi', 'Magetan',
            'Probolinggo', 'Lamongan', 'Banyuwangi', 'Blitar', 'Madiun'
        ];

        $customers = [
            ['name' => 'BP GILANG', 'email' => 'bp.gilang@example.com', 'phone' => '081234567891', 'address' => 'Alamat BP GILANG'],
            ['name' => 'GRACIA', 'email' => 'gracia@example.com', 'phone' => '081234567892', 'address' => 'Alamat GRACIA'],
            ['name' => 'PT TAN (BP TEKKO)', 'email' => 'pt.tan@example.com', 'phone' => '081234567893', 'address' => 'Alamat PT TAN (BP TEKKO)'],
            ['name' => 'AZHAR JAYA', 'email' => 'azhar.jaya@example.com', 'phone' => '081234567894', 'address' => 'Alamat AZHAR JAYA'],
            ['name' => 'BP SOLEH', 'email' => 'bp.soleh@example.com', 'phone' => '081234567895', 'address' => 'Alamat BP SOLEH'],
            ['name' => 'EKO SUPRIYADI', 'email' => 'eko.supriyadi@example.com', 'phone' => '081234567896', 'address' => 'Alamat EKO SUPRIYADI'],
            ['name' => 'PT SAMIJAYA (BU VIVI)', 'email' => 'pt.samijaya@example.com', 'phone' => '081234567897', 'address' => 'Alamat PT SAMIJAYA (BU VIVI)'],
            ['name' => 'UD BARU (CI LINKA)', 'email' => 'ud.baru@example.com', 'phone' => '081234567898', 'address' => 'Alamat UD BARU (CI LINKA)'],
            ['name' => 'UD TIMUR INDAH', 'email' => 'ud.timur.indah@example.com', 'phone' => '081234567899', 'address' => 'Alamat UD TIMUR INDAH'],
            ['name' => 'UD JATI (CIK INDAHWATI)', 'email' => 'ud.jati@example.com', 'phone' => '081234567900', 'address' => 'Alamat UD JATI (CIK INDAHWATI)'],
            ['name' => 'RUDI', 'email' => 'rudi@example.com', 'phone' => '081234567901', 'address' => 'Alamat RUDI'],
            ['name' => 'BP AHOK', 'email' => 'bp.ahok@example.com', 'phone' => '081234567902', 'address' => 'Alamat BP AHOK'],
            ['name' => 'BP WALUYO', 'email' => 'bp.waluyo@example.com', 'phone' => '081234567903', 'address' => 'Alamat BP WALUYO'],
            ['name' => 'PT ABC JAYA', 'email' => 'pt.abc.jaya@example.com', 'phone' => '081234567904', 'address' => 'Alamat PT ABC JAYA'],
            ['name' => 'TK NELAYAN (BP AMIN)', 'email' => 'tk.nelayan@example.com', 'phone' => '081234567905', 'address' => 'Alamat TK NELAYAN (BP AMIN)'],
            ['name' => 'BUDIAMAN', 'email' => 'budiaman@example.com', 'phone' => '081234567906', 'address' => 'Alamat BUDIAMAN'],
            ['name' => 'KO CUNGHO', 'email' => 'ko.cungho@example.com', 'phone' => '081234567907', 'address' => 'Alamat KO CUNGHO'],
            ['name' => 'ATIK KENJERAN', 'email' => 'atik.kenjeran@example.com', 'phone' => '081234567908', 'address' => 'Alamat ATIK KENJERAN'],
            ['name' => 'UD ARTHA MAYJEND', 'email' => 'ud.artha.mayjend@example.com', 'phone' => '081234567909', 'address' => 'Alamat UD ARTHA MAYJEND'],
            ['name' => 'EDI SIANG', 'email' => 'edi.siang@example.com', 'phone' => '081234567910', 'address' => 'Alamat EDI SIANG'],
            ['name' => 'CIK ESTER', 'email' => 'cik.ester@example.com', 'phone' => '081234567911', 'address' => 'Alamat CIK ESTER'],
            ['name' => 'BP LAN', 'email' => 'bp.lan@example.com', 'phone' => '081234567912', 'address' => 'Alamat BP LAN'],
            ['name' => 'MOSES', 'email' => 'moses@example.com', 'phone' => '081234567913', 'address' => 'Alamat MOSES'],
            ['name' => 'BP SANTOSO', 'email' => 'bp.santoso@example.com', 'phone' => '081234567914', 'address' => 'Alamat BP SANTOSO'],
            ['name' => 'EKA PS', 'email' => 'eka.ps@example.com', 'phone' => '081234567915', 'address' => 'Alamat EKA PS'],
            ['name' => 'CV PELANGI (BP ALFRED)', 'email' => 'cv.pelangi@example.com', 'phone' => '081234567916', 'address' => 'Alamat CV PELANGI (BP ALFRED)'],
            ['name' => 'CIK RANI', 'email' => 'cik.rani@example.com', 'phone' => '081234567917', 'address' => 'Alamat CIK RANI'],
            ['name' => 'CI VONY (ZZ)', 'email' => 'ci.vony@example.com', 'phone' => '081234567918', 'address' => 'Alamat CI VONY (ZZ)'],
        ];

        foreach ($customers as $customerData) {
            Customer::create(array_merge($customerData, [
                'password' => '123',
                'city' => Arr::random($cities),
            ]));
        }

        $this->command->info('Customer data berhasil dibuat!');
    }
}
