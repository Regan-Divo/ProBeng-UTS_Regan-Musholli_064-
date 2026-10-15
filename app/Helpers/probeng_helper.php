<?php

/**
 * Helper pembantu tampilan ProBeng.
 *
 * Dipasang di controller melalui:  protected $helpers = ['probeng_helper'];
 */

if (! function_exists('rupiah')) {
    /**
     * Ubah angka menjadi format mata uang Rupiah.
     */
    function rupiah($angka = 0)
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('tanggal_indonesia')) {
    /**
     * Ubah datetime database menjadi format tanggal Indonesia.
     */
    function tanggal_indonesia($tanggal = null, $denganJam = false)
    {
        if (empty($tanggal)) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $timestamp = strtotime($tanggal);
        if ($timestamp === false) {
            return '-';
        }

        $hasil = date('d', $timestamp) . ' ' . $bulan[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);

        if ($denganJam) {
            $hasil .= ' ' . date('H:i', $timestamp) . ' WIB';
        }

        return $hasil;
    }
}

if (! function_exists('badge_status')) {
    /**
     * Mengubah nilai status servis menjadi badge Bootstrap 5.
     */
    function badge_status($status)
    {
        $peta = [
            'Antre'      => 'secondary',
            'Dikerjakan' => 'warning',
            'Selesai'    => 'info',
            'Diambil'    => 'success',
        ];

        $warna = $peta[$status] ?? 'dark';

        return '<span class="badge rounded-pill bg-' . $warna . '">' . esc($status) . '</span>';
    }
}

if (! function_exists('notif_stok')) {
    /**
     * Menentukan warna badge berdasarkan jumlah stok.
     */
    function notif_stok($stok)
    {
        if ($stok <= 0) {
            return '<span class="badge rounded-pill bg-danger">Stok Habis</span>';
        }
        if ($stok <= 5) {
            return '<span class="badge rounded-pill bg-warning text-dark">' . $stok . ' (Menipis)</span>';
        }

        return '<span class="badge rounded-pill bg-success">' . $stok . '</span>';
    }
}