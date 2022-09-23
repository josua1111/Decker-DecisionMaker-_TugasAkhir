<h1>Kriteria</h1>
<table>
	<thead>
		<tr>
			<th>Kode</th>
			<th>Nama Kriteria</th>
		</tr>
	</thead>
	<?php
	$q = esc_field($_GET['q']);
	$rows = $db->get_results("SELECT * FROM tb_kriteria WHERE nama_kriteria LIKE '%$q%' ORDER BY kode_kriteria");
	foreach ($rows as $row) : ?>
		<tr>
			<td><?= $row->kode_kriteria ?></td>
			<td><?= $row->nama_kriteria ?></td>
		</tr>
	<?php endforeach; ?>
</table>