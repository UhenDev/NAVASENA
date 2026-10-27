<?php
/**
 * Generator Data Siswa Palsu (492 Siswa / 16 Kelas)
 * NAVASENA MANAGEMENT
 */

require_once dirname(__DIR__) . '/app/autoload.php';

use App\Helpers\Database;

$db = Database::getConnection();

// 1. Definisi 16 Kelas dan kuota per kelas (Total = 492)
$classDistribution = [
    'TKJ 1'    => 31,
    'TKJ 2'    => 31,
    'TKJ 3'    => 30,
    'RPL 1'    => 31,
    'RPL 2'    => 31,
    'DKV 1'    => 31,
    'DKV 2'    => 30,
    'PSPT'     => 30,
    'TKR 1'    => 31,
    'TKR 2'    => 31,
    'TEI 1'    => 31,
    'TEI 2'    => 31,
    'KIMIA 1'  => 31,
    'KIMIA 2'  => 31,
    'BUSANA 1' => 31,
    'BUSANA 2' => 30,
];

$totalTarget = array_sum($classDistribution);
if ($totalTarget !== 492) {
    die("Error: Total target bukan 492, melainkan $totalTarget\n");
}

// 2. Kumpulan nama Indonesia yang bervariasi dan natural
$maleFirstNames = [
    'Ahmad', 'Muhammad', 'Rizky', 'Fajar', 'Dimas', 'Bagus', 'Bayu', 'Aditya', 'Gilang', 'Ilham',
    'Aldi', 'Farhan', 'Hendra', 'Danu', 'Wahyu', 'Dwi', 'Kevin', 'Yoga', 'Satria', 'Reza',
    'Andika', 'Galih', 'Rian', 'Tegar', 'Bima', 'Arya', 'Denis', 'Hafiz', 'Alif', 'Raihan',
    'Rendy', 'Fauzan', 'Faisal', 'Vicky', 'Panji', 'Ardi', 'Rafi', 'Doni', 'Wildan', 'Zaki',
    'Rangga', 'Ferdi', 'Pratama', 'Sandi', 'Fikri', 'Irfan', 'Riko', 'Bobby', 'Rio', 'Naufal',
    'Brian', 'Haikal', 'Ihsan', 'Syahrul', 'Raditya', 'Rafli', 'Daffa', 'Kemal', 'Surya', 'Ananda',
    'Bagas', 'Candra', 'Dicky', 'Erick', 'Fadhil', 'Genta', 'Haris', 'Indra', 'Jofan', 'Krisna',
    'Lutfi', 'Mirza', 'Nanda', 'Oki', 'Pasha', 'Qori', 'Rafi', 'Syamil', 'Taufik', 'Umar'
];

$femaleFirstNames = [
    'Siti', 'Putri', 'Anisa', 'Nur', 'Ayu', 'Dewi', 'Fitri', 'Nabila', 'Zahra', 'Aulia',
    'Tiara', 'Dinda', 'Bella', 'Rina', 'Indah', 'Melani', 'Salma', 'Lestari', 'Amanda', 'Intan',
    'Citra', 'Cantika', 'Nadya', 'Syifa', 'Safira', 'Cindy', 'Salsabila', 'Gita', 'Vina', 'Wulan',
    'Riska', 'Ratna', 'Maharani', 'Safitri', 'Erika', 'Nadia', 'Febri', 'Tasya', 'Hana', 'Chelsea',
    'Sherly', 'Alifia', 'Devina', 'Maya', 'Tari', 'Kartika', 'Chintya', 'Dhea', 'Elvira', 'Fasya',
    'Gadis', 'Hesti', 'Isyana', 'Jovanka', 'Keisha', 'Larissa', 'Mawar', 'Nafisa', 'Olivia', 'Paramita',
    'Rania', 'Sabrina', 'Tania', 'Ulfa', 'Valerie', 'Widia', 'Yolanda', 'Zalfa', 'Aisyah', 'Bunga'
];

$middleLastNames = [
    'Pratama', 'Saputra', 'Hidayat', 'Permana', 'Santoso', 'Wijaya', 'Kusuma', 'Ramadhan',
    'Nugroho', 'Firmansyah', 'Setiawan', 'Maulana', 'Prasetyo', 'Utomo', 'Kurniawan', 'Wibowo',
    'Syahputra', 'Gunawan', 'Ardiansyah', 'Hakim', 'Fadillah', 'Lestari', 'Anggraini', 'Safitri',
    'Maharani', 'Wulandari', 'Febriani', 'Rahmawati', 'Puspitasari', 'Oktaviani', 'Nurhaliza',
    'Pratiwi', 'Sari', 'Utami', 'Maulida', 'Fauziah', 'Azzahra', 'Hermawan', 'Susanto', 'Subagyo',
    'Budiman', 'Pangestu', 'Suharto', 'Wahyudi', 'Suryana', 'Kuncoro', 'Marzuki', 'Nasution',
    'Siregar', 'Lubis', 'Kusnadi', 'Mulyadi', 'Daniswara', 'Wicaksono', 'Pambudi', 'Kusumo'
];

// Seed generator agar hasil deterministik dan rapi
mt_srand(20261027);

// Hash default untuk password siswa ('password')
$defaultPasswordHash = password_hash('password', PASSWORD_BCRYPT);

echo "Memulai pembuatan data 492 siswa...\n";

// Mulai Transaksi Database
Database::beginTransaction();

try {
    // Bersihkan data siswa lama jika ada (hanya role siswa, jangan hapus akun ketua/bendahara!)
    $db->exec("DELETE FROM users WHERE role = 'siswa'");
    $db->exec("DELETE FROM dues");
    $db->exec("DELETE FROM students");

    $stmtInsertStudent = $db->prepare("
        INSERT INTO students (student_number, full_name, class_name, gender, phone, email, status, created_at)
        VALUES (:number, :name, :class, :gender, :phone, :email, 'aktif', NOW())
    ");

    $stmtInsertUser = $db->prepare("
        INSERT INTO users (username, password_hash, full_name, role, student_id, is_active, force_change_password, created_at)
        VALUES (:username, :password, :name, 'siswa', :student_id, 1, 0, NOW())
    ");

    $stmtInsertDues = $db->prepare("
        INSERT INTO dues (student_id, total_obligation, album_portion, graduation_portion, total_paid, remaining_balance, payment_status)
        VALUES (:student_id, 250000.00, 200000.00, 50000.00, 0.00, 250000.00, 'belum_bayar')
    ");

    $nisCounter = 20261001;
    $studentsList = [];
    $usedNames = [];

    foreach ($classDistribution as $className => $count) {
        // Tentukan rasio gender per jurusan agar realistis
        $maleProbability = 50; // default 50%
        if (str_starts_with($className, 'TKR')) {
            $maleProbability = 85; // Teknik Kendaraan Ringan dominan pria
        } elseif (str_starts_with($className, 'BUSANA')) {
            $maleProbability = 15; // Busana dominan wanita
        } elseif (str_starts_with($className, 'TKJ') || str_starts_with($className, 'TEI')) {
            $maleProbability = 70; // Teknik Komputer & Elektronika
        } elseif (str_starts_with($className, 'RPL') || str_starts_with($className, 'DKV')) {
            $maleProbability = 55;
        } elseif (str_starts_with($className, 'KIMIA') || str_starts_with($className, 'PSPT')) {
            $maleProbability = 45;
        }

        for ($i = 1; $i <= $count; $i++) {
            $gender = (mt_rand(1, 100) <= $maleProbability) ? 'L' : 'P';
            
            // Generate nama lengkap
            do {
                if ($gender === 'L') {
                    $fn = $maleFirstNames[array_rand($maleFirstNames)];
                } else {
                    $fn = $femaleFirstNames[array_rand($femaleFirstNames)];
                }
                $mn = $middleLastNames[array_rand($middleLastNames)];
                $ln = (mt_rand(1, 100) <= 60) ? ' ' . $middleLastNames[array_rand($middleLastNames)] : '';
                $fullName = trim("$fn $mn$ln");
            } while (isset($usedNames[$fullName]));
            $usedNames[$fullName] = true;

            $nis = (string)$nisCounter++;
            $phonePrefix = ['0812', '0813', '0821', '0852', '0857', '0858', '0877', '0896'][mt_rand(0, 7)];
            $phone = $phonePrefix . sprintf('%08d', mt_rand(10000000, 99999999));
            $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', $fn));
            $email = $cleanName . '.' . $nis . '@navasena.sch.id';

            // 1. Simpan Siswa
            $stmtInsertStudent->execute([
                ':number' => $nis,
                ':name'   => $fullName,
                ':class'  => $className,
                ':gender' => $gender,
                ':phone'  => $phone,
                ':email'  => $email
            ]);
            $studentId = (int)$db->lastInsertId();

            // 2. Simpan User Akun
            $stmtInsertUser->execute([
                ':username'   => $nis,
                ':password'   => $defaultPasswordHash,
                ':name'       => $fullName,
                ':student_id' => $studentId
            ]);

            // 3. Simpan Dues (Kewajiban Rp 250.000)
            $stmtInsertDues->execute([
                ':student_id' => $studentId
            ]);

            $studentsList[] = [
                'nis'           => $nis,
                'nama_lengkap'  => $fullName,
                'kelas'         => $className,
                'jenis_kelamin' => $gender,
                'telepon'       => $phone,
                'email'         => $email
            ];
        }
    }

    Database::commit();
    echo "BERHASIL: 492 data siswa, akun login portal, dan kewajiban iuran berhasil dimasukkan ke database!\n";

    // 4. Buat cadangan file CSV untuk kemudahan import/rekap
    $csvFile = dirname(__DIR__) . '/storage/data_siswa_492.csv';
    $fp = fopen($csvFile, 'w');
    fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
    fputcsv($fp, ['nis', 'nama_lengkap', 'kelas', 'jenis_kelamin', 'telepon', 'email']);
    foreach ($studentsList as $s) {
        fputcsv($fp, $s);
    }
    fclose($fp);
    echo "File CSV cadangan tersimpan di: $csvFile\n";

} catch (Exception $e) {
    Database::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
