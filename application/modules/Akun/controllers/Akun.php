<?php

class Akun extends Back_controller {

	function __construct() {

		parent::__construct();
		$this->load->model('Makun', 'mdl');
		$cek = $this->session->userdata('hak_akses');

		if(!($cek)) {
			redirect('Datacenter');
		}

	}

	function index() {

		$data_array = array();

		$data_array['data']	= $this->mdl->get_data();

		$title = "Akun";
		$subtitle = "akun";
		$content = $this->load->view('lihat.php', $data_array, true);

		$this->load_content($title, $subtitle, $content);

	}

	function lihat() {

		$data_array = array();

		$data_array['data']	= $this->mdl->get_data();

		$title = "Edit Akun";
		$subtitle = "akun";
		$content = $this->load->view('edit.php', $data_array, true);

		$this->load_content($title, $subtitle, $content);

	}

	function insert() {

		$post = $this->input->post();
		$pass = $post['password'];
		unset($post['password']);
		$data = array(
			'password' => md5($pass)
		);

		$merge = array_merge($post, $data);

		$query = $this->mdl->insert_data($merge);

		$query == true ? $this->alert_info('Berhasil Tambah Data') : $this->alert_error('Gagal Tambah Data');

		redirect('Users/add');

	}

	function update() {

		$post = $this->input->post();
		$cek = $this->mdl->cek_data($post['id']);
		$media = $_FILES['foto']['name'];
		// echo $cek->photo;exit;
		if(empty($media)) {
			$name = $cek->foto;
		} else {
			$config = array(
				'file_name'    => $media,
				'upload_path'  => './assets/image/',
				'allowed_types' => 'jpg|jpeg|png',
				'max_size'      => 0
			);

			$this->upload->initialize($config);

			if(!$this->upload->do_upload('foto')) {
				$error = $this->upload->display_errors();
				$this->alert_error($error);
				redirect('Users');
			}
			$get_name = $this->upload->data();
			$name = $get_name['file_name'];
			//hapus photo sebelumnya
			unlink('assets/image/'.$cek->foto);
		}

			$data = array(
				'nama' => $post['nama'],
				'nip' => $post['nip'],
				'jabatan' => $post['jabatan'],
				'email' => $post['email'],
				'instansi' => $post['instansi'],
				'tempat_lahir' => $post['tempat_lahir'],
				'tanggal_lahir' => $post['tanggal_lahir'],
				'jenis_kelamin' => $post['jenis_kelamin'],
				'telp' => $post['telp'],
				'alamat' => $post['alamat'],
				'foto' => $name
			);

		$query = $this->mdl->update_data($data, $post['id']);

		$query == true ? $this->alert_info('Berhasil Ubah Data') : $this->alert_error('Gagal Ubah Data');

		redirect('Akun');

	}

	function delete($id) {

		$query = $this->mdl->delete_data($id);
		$query == true ? $this->alert_info('Berhasil Hapus Data') : $this->alert_error('Gagal Hapus Data');
		redirect('Users');

	}

	function get_data() {
		$fetch_data = $this->mdl->make_datatables();
		$data = array();
		$no=1;
		foreach($fetch_data as $row)
		{
			if($row->hak_akses != 1) {
				 $sub_array = array();
				 $sub_array[] = $no++;
				 $sub_array[] = $row->nama;
				 $sub_array[] = $row->email;
				 $sub_array[] = $row->hak_akses==2?"<span class='badge badge-secondary'>Admin</span>":"<span class='badge badge-secondary'>Superadmin</span>";
				 $sub_array[] = $row->status==0?"<span class='badge badge-secondary'>Nonaktif</span>":"<span class='badge badge-secondary'>Aktif</span>";
				 if($row->hak_akses == 1) {
					 $sub_array[] = "";
				 } else {
					 $sub_array[] = '<a href="'.site_url('Users/edit/'.$row->id).'" class="btn btn-info btn-xs update">Edit</a>
					 <a href="'.site_url('Users/delete/'.$row->id).'" onclick="return confirm(\'Apakah anda yakin?\')" class="btn btn-danger btn-xs delete">Delete</a>';
				 }

				 $data[] = $sub_array;
			 }
		}
		$output = array(
				 "draw"                    =>     intval($_POST["draw"]),
				 "recordsTotal"          =>      $this->mdl->get_all_data(),
				 "recordsFiltered"     =>     $this->mdl->get_filtered_data(),
				 "data"                    =>     $data
		);
		echo json_encode($output);

	}


}
