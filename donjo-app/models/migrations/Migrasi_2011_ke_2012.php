<?php

/**
 * File ini:
 *
 * Model untuk migrasi database
 *
 * donjo-app/models/migrations/Migrasi_2011_ke_2012.php
 *
 */

/**
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:

 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.

 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package	OpenSID
 * @author	Tim Pengembang OpenDesa
 * @copyright	Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright	Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license	http://www.gnu.org/licenses/gpl.html	GPL V3
 * @link 	https://github.com/OpenSID/OpenSID
 */

class Migrasi_2011_ke_2012 extends MY_model {

	public function up()
	{
		$hasil = true;

		// Tambah kolom masa_berlaku & satuan_masa_berlaku di tweb_surat_format
		if ( ! $this->db->field_exists('masa_berlaku', 'tweb_surat_format'))
		{
			$fields = [
				'masa_berlaku' => [
					'type' => 'INT',
					'constraint' => 3,
					'default' => '1'
				],
				'satuan_masa_berlaku' => [
					'type' => 'VARCHAR',
					'constraint' => 15,
					'default' => 'M'
				]
			];

			$hasil = $this->dbforge->add_column('tweb_surat_format', $fields);
		}

		status_sukses($hasil);

		// Pengaturan Token TrackSID
		if ( ! $this->db->field_exists('token_opensid', 'setting_aplikasi'))
		{
			$query = "
				INSERT INTO `setting_aplikasi` (`id`, `key`, `value`, `keterangan`, `jenis`, `kategori`) VALUES
				(43, 'token_opensid', '', 'Token OpenSID', '', 'sistem')
				ON DUPLICATE KEY UPDATE `key` = VALUES(`key`), keterangan = VALUES(keterangan), jenis = VALUES(jenis), kategori = VALUES(kategori)";
			$this->db->query($query);
  	}

		// Ubah struktur table program_peserta
		$hasil =& $this->db->query('ALTER TABLE `program_peserta` CHANGE COLUMN `kartu_id_pend` `kartu_id_pend` INT(11) NULL DEFAULT NULL AFTER `no_id_kartu`');
		$hasil =& $this->db->query('ALTER TABLE `program_peserta` CHANGE COLUMN `program_id` `program_id` INT(11) NOT NULL AFTER `id`');
		// Ganti paramter menjadi id u/ tiap sasaran
		$list_peserta = $this->db
			->select('pp.*, p.sasaran')
			->from('program_peserta pp')
			->join('program p', 'p.id = pp.program_id')
			->get()
			->result_array();

		foreach ($list_peserta as $peserta)
		{
			switch ($peserta['sasaran'])
			{
				// Penduduk
				case 1:
					$id = $this->db->select('id')->get_where('tweb_penduduk', ['nik' => $peserta['peserta']])->row()->id;
					break;

				// Keluarga
				case 2:
					$id = $this->db->select('id')->get_where('tweb_keluarga', ['no_kk' => $peserta['peserta']])->row()->id;
					break;

				// Rumah Tangga
				case 3:
					// no_kk = no_rtm (lain kali disesuaikan)
					$id = $this->db->select('id')->get_where('tweb_rtm', ['no_kk' => $peserta['peserta']])->row()->id;
					break;

				// Kelompok
				default:
					// Krn peserta sasaran kelompok sdah menggunakan id, maka tdk perlu di ubah lg.
					$id = $peserta['peserta'];
					break;
			}

			$this->db->where('id', $peserta['id'])->update('program_peserta', ['peserta' => $id]);
		}

		status_sukses($hasil);
	}

}
