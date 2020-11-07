<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File ini:
 *
 * Controller untuk modul Program Bantuan
 *
 * donjo-app/controllers/Program_bantuan.php
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

class Program_bantuan extends Admin_Controller {

	private $_set_page;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['program_bantuan_model', 'config_model']);
		$this->modul_ini = 6;
		//$this->_set_page = ['20', '50', '100'];
		$this->_set_page = ['1', '2', '100'];
	}

	public function form_($program_id = 0)
	{
		$data['program'] = $this->program_bantuan_model->get_program(1, $program_id);
		$sasaran = $data['program'][0]['sasaran'];
		$nik = $this->input->post('nik');

		if (isset($nik))
		{
			$data['individu'] = $this->program_bantuan_model->get_peserta($nik, $sasaran);
			$data['individu']['program'] = $this->program_bantuan_model->get_peserta_program($sasaran, $data['individu']['id_peserta']);
		}
		else
		{
			$data['individu'] = NULL;
		}

		$data['form_action'] = site_url("program_bantuan/add_peserta/".$program_id);

		$this->render('program_bantuan/form', $data);
	}

	public function detail($program_id = 0, $p = 1)
	{
		$per_page = $this->input->post('per_page');
		if (isset($per_page))
			$this->session->per_page = $per_page;

		$data['cari'] = $this->session->cari ?: '';
		$data['program'] = $this->program_bantuan_model->get_program($p, $program_id);
		$data['keyword'] = $this->program_bantuan_model->autocomplete($program_id, $this->input->post('cari'));
		$data['paging'] = $data['program'][0]['paging'];
		$data['p'] = $p;
		$data['func'] = "detail/$program_id";
		$data['per_page'] = $this->session->per_page;
		$data['set_page'] = $this->_set_page;
		$this->set_minsidebar(1);

		$this->render('program_bantuan/detail', $data);
	}

	// $id = program_peserta.id
	public function peserta($cat = 0, $id = 0)
	{
		$data = $this->program_bantuan_model->get_peserta_program($cat, $id);

		$this->render('program_bantuan/peserta', $data);
	}

	// $id = program_peserta.id
	public function data_peserta($id = 0)
	{
		$data['peserta'] = $this->program_bantuan_model->get_program_peserta_by_id($id);

		switch ($data['peserta']['sasaran'])
		{
			case '1':
			case '2':
				$peserta_id = $data['peserta']['kartu_id_pend'];
				break;
			case '3':
			case '4':
				$peserta_id = $data['peserta']['peserta'];
				break;
		}
		$data['individu'] = $this->program_bantuan_model->get_peserta($peserta_id, $data['peserta']['sasaran']);
		$data['individu']['program'] = $this->program_bantuan_model->get_peserta_program($data['peserta']['sasaran'], $data['peserta']['peserta']);
		$data['detail'] = $this->program_bantuan_model->get_data_program($data['peserta']['program_id']);
		$this->set_minsidebar(1);

		$this->render('program_bantuan/data_peserta', $data);
	}

	public function add_peserta($program_id = 0)
	{
		$this->program_bantuan_model->add_peserta($program_id);

		$redirect = ($this->session->userdata('aksi') != 1) ? $_SERVER['HTTP_REFERER'] : "program_bantuan/detail/$program_id";

		$this->session->unset_userdata('aksi');

		redirect($redirect);
	}

	public function aksi($aksi = '', $program_id = 0)
	{
		$this->session->set_userdata('aksi', $aksi);

		redirect("program_bantuan/form/$program_id");
	}

	public function hapus_peserta($program_id = 0, $peserta_id = '')
	{
		$this->redirect_hak_akses('h', "program_bantuan/detail/$program_id");
		$this->program_bantuan_model->hapus_peserta($peserta_id);
		redirect("program_bantuan/detail/$program_id");
	}

	public function delete_all($program_id = 0)
	{
		$this->redirect_hak_akses('h', "program_bantuan/detail/$program_id");
		$this->program_bantuan_model->delete_all();
		redirect("program_bantuan/detail/$program_id");
	}

	// $id = program_peserta.id
	public function edit_peserta($id = 0)
	{
		$this->program_bantuan_model->edit_peserta($id);
		$program_id = $this->input->post('program_id');
		redirect("program_bantuan/detail/$program_id");
	}

	// $id = program_peserta.id
	public function edit_peserta_form($id = 0)
	{
		$data = $this->program_bantuan_model->get_program_peserta_by_id($id);
		$data['form_action'] = site_url("program_bantuan/edit_peserta/$id");
		$this->load->view('program_bantuan/edit_peserta', $data);
	}

	// $id = program.id
	public function update($id)
	{
		$this->program_bantuan_model->update_program($id);
		redirect("program_bantuan/detail/$id");
	}

	/*
	 * $aksi = cetak/unduh
	 */
	public function daftar($program_id = 0, $aksi = '')
	{
		if ($program_id > 0)
		{
			$temp = $this->session->per_page;
			$this->session->per_page = 1000000000; // Angka besar supaya semua data terunduh
			$data["sasaran"] = unserialize(SASARAN);

			$data['config'] = $this->config_model->get_data();
			$data['peserta'] = $this->program_bantuan_model->get_program(1, $program_id);
			$data['aksi'] = $aksi;
			$this->session->per_page = $temp;

			$this->load->view("program_bantuan/$aksi", $data);
		}
	}

	public function search($program_id = 0)
	{
		$cari = $this->input->post('cari');

		if ($cari != '')
			$this->session->cari = $cari;
		else $this->session->unset_userdata('cari');

		redirect("program_bantuan/detail/$program_id");
	}

	// -------------------------------- Perbaikan --------------------------------
	// - Program Bantuan
	public function clear()
	{
		$this->session->per_page = $this->_set_page[0];
		$this->session->unset_userdata(['sasaran' , 'order_by']);
		redirect('program_bantuan');
	}

	public function index($p = 1)
	{
		$data['sasaran'] = $this->session->sasaran ?: '';
		$data['list_sasaran'] = unserialize(SASARAN);
		$data['order_by'] = $this->session->order_by ?: '';

		$per_page = $this->input->post('per_page');
		if (isset($per_page))
			$this->session->per_page = $per_page;

		$data['func'] = 'index';
		$data['set_page'] = $this->_set_page;
		$data['paging'] = $this->program_bantuan_model->paging_program($p);
		$data['main'] = $this->program_bantuan_model->list_data_program($data['order_by'], $data['paging']->offset, $data['paging']->per_page);

		$this->render('program_bantuan/program', $data);
		//echo json_encode($data, TRUE);
	}

	public function form_program($id = 0)
	{
		if ($id)
		{
			$data['form_action'] = site_url("program_bantuan/ubah_program/$id");
			$data['program'] = $this->program_bantuan_model->get_data_program($id);
		}
		else
		{
			$data['form_action'] = site_url("program_bantuan/tambah_program");
			$data['program'] = NULL;
		}

		$data['asaldana'] = unserialize(ASALDANA);
		$data['list_sasaran'] = unserialize(SASARAN);

		$this->render('program_bantuan/form_program', $data);
		//echo json_encode($data, TRUE);
	}

	public function tambah_program()
	{
		$this->program_bantuan_model->tambah_program();
		redirect("program_bantuan");
	}

	public function ubah_program($id)
	{
		$this->program_bantuan_model->ubah_program($id);
		redirect("program_bantuan");
	}

	public function hapus_program($id)
	{
		$this->redirect_hak_akses('h', "program_bantuan");
		$this->program_bantuan_model->hapus_program($id);
		redirect("program_bantuan");
	}

	public function filter($filter = '', $page = 1, $order_by = 0)
	{
		//if ($filter == "dusun") $this->session->unset_userdata(['rw', 'rt']);
		//if ($filter == "rw") $this->session->unset_userdata("rt");

		$value = $order_by ?: $this->input->post($filter);
		if ($value != "")
			$this->session->$filter = $value;
		else $this->session->unset_userdata($filter);

		if ($page > 1) $link = "/index/$page";
		redirect("program_bantuan" . "$link");
	}

}
