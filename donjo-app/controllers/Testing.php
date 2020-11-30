<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends CI_Controller {

	public function index()
	{
		$data = $this->list_menu_kiri();

		echo json_encode($data, TRUE);
	}

	private function list_kategori($parrent = 0)
	{
		$data = $this->db
			->where('enabled', 1)
			->where('parrent', $parrent)
			->order_by('urut')
			->get('kategori')
			->result_array();

		return $data;
	}

	public function list_menu_kiri()
	{
		$data	= $this->list_kategori();

		foreach ($data AS $key => $sub_menu) {
			if ($sub_menu['id']) $data[$key]['submenu'] = $this->list_kategori($sub_menu['id']);
		}

		return $data;
	}

}
