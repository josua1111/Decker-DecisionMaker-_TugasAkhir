<div class="page-header">
    <h1>Nilai Bobot Sub</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="rel_sub" />
            <div class="form-group">
                <select class="form-control" name="kode_kriteria" onchange="this.form.submit()">
                    <option value="">Pilih kriteria</option>
                    <?= get_kriteria_option(set_value('kode_kriteria')) ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> Refresh</button>
            </div>
        </form>
    </div>
    <?php
    $kode_kriteria = $_GET['kode_kriteria'];
    if ($kode_kriteria) : ?>
        <div class="panel-body">
            <?php
            if ($_POST) include 'aksi.php';
            $rel_sub = get_rel_sub($kode_kriteria);
            $ahp = new AHP($rel_sub);
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <form class="form-inline" method="post">
                        <div class="form-group">
                            <select class="form-control" name="ID1">
                                <?= get_sub_option($kode_kriteria, $_POST['ID1']) ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="nilai">
                                <?= get_nilai_option($_POST['nilai']) ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="ID2">
                                <?= get_sub_option($kode_kriteria, $_POST['ID2']) ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span> Ubah</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <?php foreach ($KRITERIA_SUB[$kode_kriteria] as $key => $val) : ?>
                                    <th><?= $key ?></th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <?php
                        $no = 1;
                        $a = 1;
                        foreach ($rel_sub as $key => $val) : ?>
                            <tr>
                                <td><?= $key ?></td>
                                <td><?= $SUB[$key]->nama_sub ?></td>
                                <?php
                                $b = 1;
                                foreach ($val as $k => $v) : ?>
                                    <td class="<?= $a == $b ? 'success' : ($a < $b ? 'danger' : '') ?>"><?= round($v, 3) ?></td>
                                <?php $b++;
                                endforeach ?>
                            </tr>
                        <?php $a++;
                        endforeach ?>
                    </table>
                </div>
                <div class="panel-body">

                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <?php foreach ($KRITERIA_SUB[$kode_kriteria] as $key => $val) : ?>
                                    <th><?= $key ?></th>
                                <?php endforeach ?>
                                <th>Prioritas</th>
                                <th>Consistency Measure</th>
                            </tr>
                        </thead>
                        <?php foreach ($ahp->normal as $key => $val) :
                            $db->query("UPDATE tb_sub SET bobot_sub='{$ahp->prioritas[$key]}' WHERE kode_sub='$key'") ?>
                            <tr>
                                <td><?= $key ?></td>
                                <?php foreach ($val as $k => $v) : ?>
                                    <td><?= round($v, 4) ?></td>
                                <?php endforeach ?>
                                <td><?= round($ahp->prioritas[$key], 4) ?></td>
                                <td><?= round($ahp->cm[$key], 4) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                </div>
                <div class="panel-footer">
                    <?php
                    $cm = $ahp->cm;
                    $CI = count($cm) > 1 ? ((array_sum($cm) / count($cm)) - count($cm)) / (count($cm) - 1) : 0;
                    $RI = $nRI[count($rel_sub)];
                    $CR = $RI == 0 ? 0 : $CI / $RI;
                    echo "<p>Consistency Index: " . round($CI, 3) . "<br />";
                    echo "Ratio Index: " . round($RI, 3) . "<br />";
                    echo "Consistency Ratio: " . round($CR, 3);
                    if ($CR > 0.10) {
                        echo " (Tidak konsisten)<br />";
                    } else {
                        echo " (Konsisten)<br />";
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>