<div class="page-header">
    <h1>Nilai Bobot Alternatif</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="rel_alternatif" />
            <div class="form-group">
                <select class="form-control" name="kode_sub" onchange="this.form.submit()">
                    <option value="">Pilih sub</option>
                    <?= get_sub_all_option(set_value('kode_sub')) ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> Refresh</button>
            </div>
        </form>
    </div>
    <?php
    $kode_sub = $_GET['kode_sub'];
    if ($kode_sub) : ?>
        <div class="panel-body">
            <?php
            if ($_POST) include 'aksi.php';
            $rel_alternatif = get_rel_alternatif($kode_sub);
            $ahp = new AHP($rel_alternatif);
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <form class="form-inline" method="post">
                        <div class="form-group">
                            <select class="form-control" name="ID1">
                                <?= get_alternatif_option($_POST['ID1']) ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="nilai">
                                <?= get_nilai_option($_POST['nilai']) ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="ID2">
                                <?= get_alternatif_option($_POST['ID2']) ?>
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
                                <?php foreach ($ALTERNATIF as $key => $val) : ?>
                                    <th><?= $key ?></th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <?php
                        $no = 1;
                        $a = 1;
                        foreach ($rel_alternatif as $key => $val) : ?>
                            <tr>
                                <td><?= $key ?></td>
                                <td><?= $ALTERNATIF[$key]->nama_alternatif ?></td>
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
                                <?php foreach ($ALTERNATIF as $key => $val) : ?>
                                    <th><?= $key ?></th>
                                <?php endforeach ?>
                                <th>Prioritas</th>
                                <th>Consistency Measure</th>
                            </tr>
                        </thead>
                        <?php foreach ($ahp->normal as $key => $val) :
                            $db->query("UPDATE tb_alternatif_sub SET bobot_alternatif='{$ahp->prioritas[$key]}' WHERE kode_alternatif='$key' AND kode_sub='$kode_sub'") ?>
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
                    $RI = $nRI[count($rel_alternatif)];
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