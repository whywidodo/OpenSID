<div class="content-wrapper">
	<section class="content-header">
		<h1>Daftar Program Bantuan</h1>
		<ol class="breadcrumb">
			<li><a href="<?=site_url('hom_sid')?>"><i class="fa fa-home"></i> Home</a></li>
			<li class="active">Daftar Program Bantuan</li>
		</ol>
	</section>
	<section class="content" id="maincontent">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header with-border">
						<a href="<?=site_url('program_bantuan/create')?>" class="btn btn-social btn-flat bg-olive btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Tambah Program Bantuan Baru"><i class="fa fa-plus"></i> Tambah Program Bantuan</a>
						<a href="<?= site_url("{$this->controller}/clear") ?>" class="btn btn-social btn-flat bg-purple btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"><i class="fa fa-refresh"></i>Bersihkan</a>
						<a href="<?=site_url('program_bantuan/panduan')?>" class="btn btn-social btn-flat btn-info btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Tambah Program Bantuan Baru"><i class="fa fa-question-circle"></i> Panduan</a>
						<?php if ($tampil != 0): ?>
							<a href="<?=site_url('program_bantuan')?>" class="btn btn-social btn-flat btn-info btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Kembali Ke Daftar Program Bantuan"><i class="fa fa-arrow-circle-o-left"></i> Kembali Ke Daftar Program Bantuan</a>
						<?php endif; ?>
					</div>
					<div class="box-body">
						<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
							<div class="row">
								<div class="col-sm-9">
									<form id="mainform" name="mainform" action="" method="post">
										<select class="form-control input-sm" name="sasaran" onchange="formAction('mainform', '<?= site_url('program_bantuan/filter/sasaran'); ?>')">
											<option value="">Pilih Sasaran</option>
											<?php foreach ($list_sasaran AS $key => $value): ?>
												<option value="<?= $key; ?>" <?= selected($set_sasaran, $key); ?>><?= $value; ?></option>
											<?php endforeach; ?>
										</select>
									</form>
								</div>
							</div>
							<div class="table-responsive">
									<table class="table table-bordered table-striped dataTable table-hover tabel-daftar">
										<thead class="bg-gray disabled color-palette">
											<tr>
												<th>No</th>
												<th>Aksi</th>
												<th><?= url_order($order_by, "{$this->controller}/filter/order_by/$paging->page", 1, 'Nama Program'); ?></th>
												<th><?= url_order($order_by, "{$this->controller}/filter/order_by/$paging->page", 3, 'Asal Dana'); ?></th>
												<th><?= url_order($order_by, "{$this->controller}/filter/order_by/$paging->page", 5, 'Jumlah Peserta'); ?></th>
												<th>Masa Berlaku</th>
												<th><?= url_order($order_by, "{$this->controller}/filter/order_by/$paging->page", 7, 'Sasaran'); ?></th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($main as $key => $data): ?>
												<tr>
													<td class="padat"><?= ($paging->offset + $key + 1) ; ?></td>
													<td class="aksi">
														<a href="<?= site_url("program_bantuan/detail/$data[id]")?>" class="btn bg-purple btn-flat btn-sm"  title="Rincian"><i class="fa fa-list"></i></a>
														<a href="<?= site_url("program_bantuan/edit/$data[id]")?>" class="btn bg-orange btn-flat btn-sm"  title="Ubah"><i class="fa fa-edit"></i></a>
														<?php if ($this->CI->cek_hak_akses('h')): ?>
															<a href="#" data-href="<?= site_url("program_bantuan/hapus/$data[id]")?>" class="btn bg-maroon btn-flat btn-sm <?= ($data[jml_peserta] <= 1)?:'disabled'; ?>"  title="Hapus" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i></a>
														<?php endif ?>
													</td>
													<td nowrap><a href="<?= site_url("program_bantuan/detail/$data[id]")?>"><?= $data['nama']; ?></a></td>
													<td><?= $data['asaldana']; ?></td>
													<td class="padat"><?= $data['jml_peserta']; ?></td>
													<td nowrap><?= fTampilTgl($data['sdate'], $data['edate']); ?></td>
													<td nowrap><?= $list_sasaran[$data['sasaran']]; ?></td>
													<td class="padat"><?= ($data['status'] == 1) ? 'Aktif' : 'Tidak Aktif' ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
							</div>
							<?php $this->load->view('global/paging'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php $this->load->view('global/confirm_delete'); ?>
