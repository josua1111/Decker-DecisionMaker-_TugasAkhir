<h1>Kriteria</h1>
<table>
	<thead>
		<tr>
			<th>Kriteria</th>
			<th>Kode</th>
			<th>Nama Kriteria</th>
		</tr>
	</thead>
	<?php
	$q = esc_field($_GET['q']);
	$rows = $db->get_results("SELECT * FROM tb_sub WHERE nama_sub LIKE '%$q%' ORDER BY kode_kriteria, kode_sub");
	foreach ($rows as $row) : ?>
		<tr>
			<td><?= $KRITERIA[$row->kode_kriteria]->nama_kriteria ?></td>
			<td><?= $row->kode_sub ?></td>
			<td><?= $row->nama_sub ?></td>
		</tr>
	<?php endforeach; ?>
</table>