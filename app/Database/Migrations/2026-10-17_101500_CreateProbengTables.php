<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Pembuatan tabel-tabel ProBeng.
 *
 * Terjemahan ERD:
 *   pelanggan  1 ----< N  servis  1 ----< N  detail_servis  N >---- 1  sparepart
 */
class CreateProbengTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pelanggan'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_pelanggan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'no_telepon'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'plat_nomor'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'jenis_kendaraan' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('id_pelanggan', true);
        $this->forge->addUniqueKey('plat_nomor');
        $this->forge->createTable('pelanggan', true);

        $this->forge->addField([
            'id_sparepart' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_barang'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'harga_beli'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'harga_jual'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'stok'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id_sparepart', true);
        $this->forge->createTable('sparepart', true);

        $this->forge->addField([
            'id_servis'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_pelanggan'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'keluhan'        => ['type' => 'TEXT'],
            'biaya_jasa'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status_servis'  => ['type' => 'VARCHAR', 'constraint' => 15, 'default' => 'Antre'],
            'tanggal_masuk'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id_servis', true);
        $this->forge->addForeignKey('id_pelanggan', 'pelanggan', 'id_pelanggan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('servis', true);

        $this->forge->addField([
            'id_detail'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_servis'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_sparepart' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jumlah'       => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'subtotal'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id_detail', true);
        $this->forge->addForeignKey('id_servis', 'servis', 'id_servis', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_sparepart', 'sparepart', 'id_sparepart', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('detail_servis', true);
    }

    public function down()
    {
        $this->forge->dropTable('detail_servis', true);
        $this->forge->dropTable('servis', true);
        $this->forge->dropTable('sparepart', true);
        $this->forge->dropTable('pelanggan', true);
    }
}