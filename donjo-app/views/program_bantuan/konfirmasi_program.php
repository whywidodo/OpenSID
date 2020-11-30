<div class="box-header with-border">
	<h5><b>Rincian Program</b></h5>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover tabel-rincian">
			<tbody>
				<tr>
					<td width="20%">Nama Program</td>
					<td width="1">:</td>
					<td><?= strtoupper($program['nama']); ?></td>
				</tr>
				<tr>
					<td>Sasaran Peserta</td>
					<td>:</td>
					<td><?= $list_sasaran[$program['sasaran']]; ?></td>
				</tr>
				<tr>
					<td>Masa Berlaku</td>
					<td>:</td>
					<td><?= fTampilTgl($program['sdate'], $program['edate']) . ' ' .$program['staus']; ?></td>
				</tr>
				<tr>
					<td>Keterangan</td>
					<td>:</td>
					<td><?= $program['ndesc']; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
