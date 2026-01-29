<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WisatawanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan tabel
        DB::table('wisatawans')->truncate();

        $pathNegara = database_path('seeders/csv/negara.csv');
        $pathPariwisata = database_path('seeders/csv/pariwisata.csv');

        if (!file_exists($pathPariwisata)) {
            $this->command->error("File pariwisata.csv tidak ditemukan!");
            return;
        }

        // Koordinat Map
        $coords = [
            
            'IDN' => [-0.789275, 113.921327],
            'MYS' => [4.210484, 101.975766],
            'SGP' => [1.352083, 103.819836],
            'THA' => [15.870032, 100.992541],
            'PHL' => [12.879721, 121.774017],
            'VNM' => [14.058324, 108.277199],
            'MMR' => [21.916221, 95.956023],
            'TLS' => [-8.874217, 125.727539],
            'BRN' => [4.535277, 114.727669],
            'KHM' => [12.565679, 104.990963],
            'LAO' => [19.85627, 102.495496],

            
            'CHN' => [35.86166, 104.195397],
            'JPN' => [36.204824, 138.252924],
            'KOR' => [35.907757, 127.766922],
            'IND' => [20.593684, 78.96288],
            'AUS' => [-25.274398, 133.775136],
            'NZL' => [-40.900557, 174.885971],
            'HKG' => [22.396428, 114.109497],
            'TWN' => [23.69781, 120.960515],
            'BGD' => [23.684994, 90.356331],
            'PAK' => [30.375321, 69.345116],
            'PNG' => [-6.314993, 143.95555],

            
            'NLD' => [52.132633, 5.291266],
            'DEU' => [51.165691, 10.451526],
            'FRA' => [46.227638, 2.213749],
            'GBR' => [55.378051, -3.435973],
            'ITA' => [41.87194, 12.56738],
            'ESP' => [40.463667, -3.74922],
            'RUS' => [61.52401, 105.318756],
            'SWE' => [60.128161, 18.643501],
            'DNK' => [56.26392, 9.501785],
            'UKR' => [48.379433, 31.16558],
            'POL' => [51.919438, 19.145136],
            'CHE' => [46.818188, 8.227512],

            
            'USA' => [37.09024, -95.712891],
            'CAN' => [56.130366, -106.346771],
            'BRA' => [-14.235004, -51.92528],
            'SAU' => [23.885942, 45.079162],
            'ZAF' => [-30.559482, 22.937506],
            'ARE' => [23.424076, 53.847818],
        ];

        // 2. Baca Negara
        $mapNegara = [
            'MYS' => 'Malaysia',
            'SGP' => 'Singapura',
            'CHN' => 'Tiongkok',
            'AUS' => 'Australia',
            'TLS' => 'Timor Leste',
            'IND' => 'India',
            'JPN' => 'Jepang',
            'KOR' => 'Korea Selatan',
            'USA' => 'Amerika Serikat',
            'GBR' => 'United Kingdom',
            'RUS' => 'Rusia',
            'TWN' => 'Taiwan',
            'SAU' => 'Arab Saudi',
            'FRA' => 'Prancis',
            'PHL' => 'Filipina',
            'DEU' => 'Jerman',
            'NLD' => 'Belanda',
            'VNM' => 'Vietnam',
            'CAN' => 'Kanada',
            'NZL' => 'Selandia Baru',
            'PNG' => 'Papua Nugini',
        ];

        // 3. Baca Pariwisata & Grouping
        $file = fopen($pathPariwisata, 'r');
        fgetcsv($file, 0, ";");

        $groupedData = [];

        while (($row = fgetcsv($file, 0, ";")) !== FALSE) {
            if (count($row) < 4)
                continue;

            $iso = $row[0];
            $tahun = (int) $row[2]; 
            $jumlah = (int) $row[3]; 

            if (!isset($groupedData[$iso]))
                $groupedData[$iso] = [];
            if (!isset($groupedData[$iso][$tahun])) {
                $groupedData[$iso][$tahun] = [];
            }

            if (count($groupedData[$iso][$tahun]) < 5) {
                $groupedData[$iso][$tahun][] = $jumlah;
            }
        }
        fclose($file);

        // 4. Insert ke Database
        foreach ($groupedData as $iso => $tahunList) {
            foreach ($tahunList as $tahun => $counts) {

                $lat = $coords[$iso][0] ?? rand(-5, 5);
                $lng = $coords[$iso][1] ?? rand(100, 120);

                DB::table('wisatawans')->insert([
                    'kode_iso' => $iso,
                    'nama_negara' => $mapNegara[$iso] ?? $iso,
                    'tahun' => $tahun,
                    'januari' => $counts[0] ?? 0,
                    'februari' => $counts[1] ?? 0,
                    'maret' => $counts[2] ?? 0,
                    'april' => $counts[3] ?? 0,
                    'mei' => $counts[4] ?? 0,
                    'lat' => $lat,
                    'lng' => $lng,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info("Seeding Berhasil! Data 2020-2025 sudah masuk.");
    }
}