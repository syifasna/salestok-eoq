<?php

if (!function_exists('tanggal_indonesia')) {
    function tanggal_indonesia($tanggal, $tampil_hari = true)
    {
        $nama_hari = [
            1 => 'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu'
        ];

        $nama_bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $tahun = substr($tanggal, 0, 4);
        $bulan = $nama_bulan[(int) substr($tanggal, 5, 2)];
        $tgl   = substr($tanggal, 8, 2);

        $text = $tgl . ' ' . $bulan . ' ' . $tahun;

        if ($tampil_hari) {
            $hari = date('N', strtotime($tanggal));
            return $nama_hari[$hari] . ', ' . $text;
        }

        return $text;
    }
}

if (!function_exists('tambah_nol_didepan')) {
    function tambah_nol_didepan($value, $threshold = 5)
    {
        return str_pad($value, $threshold, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('format_uang')) {
    function format_uang($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (! function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs($angka);
        $baca  = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

        if ($angka < 12) {
            return " " . $baca[$angka];
        } elseif ($angka < 20) {
            return terbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            return terbilang(intval($angka / 10)) . " puluh" . terbilang($angka % 10);
        } elseif ($angka < 200) {
            return " seratus" . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return terbilang(intval($angka / 100)) . " ratus" . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return " seribu" . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return terbilang(intval($angka / 1000)) . " ribu" . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return terbilang(intval($angka / 1000000)) . " juta" . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return terbilang(intval($angka / 1000000000)) . " miliar" . terbilang($angka % 1000000000);
        } else {
            return "Angka terlalu besar";
        }
    }
}
