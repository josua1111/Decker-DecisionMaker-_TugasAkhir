<div class="page-header">
    <h1>Perhitungan</h1>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a data-toggle="collapse" href="#bobot">
                Bobot Kriteria dan Sub
            </a>
        </h3>
    </div>
    <div class="table-responsive collapse in" id="bobot">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Kriteria</th>
                    <th>Sub</th>
                    <th>Bobot Kriteria</th>
                    <th>Bobot Sub</th>
                    <th>Bobot Akhir</th>
                </tr>
            </thead>
            <?php
            $sub_bobot = get_sub_bobot();
            foreach ($SUB as $key => $val) : ?>
                <tr>
                    <td><?= $val->kode_kriteria ?> - <?= $KRITERIA[$val->kode_kriteria]->nama_kriteria ?></td>
                    <td><?= $key ?> - <?= $val->nama_sub ?></td>
                    <td><?= round($KRITERIA[$val->kode_kriteria]->bobot_kriteria, 4) ?></td>
                    <td><?= round($val->bobot_sub, 4) ?></td>
                    <td><?= round($sub_bobot[$key], 4) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a data-toggle="collapse" href="#alternatif_kriteria">
                Alternatif Sub
            </a>
        </h3>
    </div>
    <div class="table-responsive collapse in" id="alternatif_kriteria">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <?php foreach ($SUB as $key => $val) : ?>
                        <th><?= $val->nama_sub ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <?php
            $alternatif_sub = get_alternatif_sub();
            foreach ($alternatif_sub as $key => $val) : ?>
                <tr>
                    <td><?= $key ?></td>
                    <td><?= $ALTERNATIF[$key]->nama_alternatif ?></td>
                    <?php foreach ($val as $k => $v) : ?>
                        <td><?= round($v, 4) ?></td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a data-toggle="collapse" href="#terbobot">
                Terbobot
            </a>
        </h3>
    </div>
    <div class="table-responsive collapse in" id="terbobot">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <?php foreach ($SUB as $key => $val) : ?>
                        <th><?= $val->nama_sub ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <?php
            $terbobot = get_terbobot($alternatif_sub, $sub_bobot);
            foreach ($terbobot as $key => $val) : ?>
                <tr>
                    <td><?= $key ?></td>
                    <td><?= $ALTERNATIF[$key]->nama_alternatif ?></td>
                    <?php foreach ($val as $k => $v) : ?>
                        <td><?= round($v, 4) ?></td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Perangkingan</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <tr>
                <th>Rank</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Total</th>
            </tr>
            <?php
            $total = get_total($terbobot);
            $rank = get_rank($total);
            foreach ($rank as $key => $val) :
                $db->query("UPDATE tb_alternatif SET total='{$total[$key]}', rank='{$rank[$key]}' WHERE kode_alternatif='$key'");
            ?>
                <tr>
                    <td><?= $val ?></td>
                    <td><?= $key ?></td>
                    <td><?= $ALTERNATIF[$key]->nama_alternatif ?></td>
                    <td><?= round($total[$key], 4) ?></td>
                </tr>
            <?php $no++;
            endforeach ?>
        </table>
    </div>
    <div class="panel-body">
        <style>
            .highcharts-credits {
                display: none;
            }
        </style>
        <?php
        function get_chart1()
        {
            global $total, $ALTERNATIF;

            foreach ($total as $key => $val) {
                $data[$ALTERNATIF[$key]->nama_alternatif] = $val * 1;
            }

            $chart = array();

            $chart['chart']['type'] = 'column';
            $chart['chart']['options3d'] = array(
                'enabled' => true,
                'alpha' => 15,
                'beta' => 15,
                'depth' => 50,
                'viewDistance' => 25,
            );
            $chart['title']['text'] = 'Grafik Hasil Perangkingan';
            $chart['plotOptions'] = array(
                'column' => array(
                    'depth' => 25,
                )
            );

            $chart['xAxis'] = array(
                'categories' => array_keys($data),
            );
            $chart['yAxis'] = array(
                'min' => 0,
                'title' => array('text' => 'Total'),
            );
            $chart['tooltip'] = array(
                'headerFormat' => '<span style="font-size:10px">{point.key}</span><table>',
                'pointFormat' => '<tr><td style="color:{series.color};padding:0">{series.name}: </td>
                            <td style="padding:0"><b>{point.y:.3f}</b></td></tr>',
                'footerFormat' => '</table>',
                'shared' => true,
                'useHTML' => true,
            );

            $chart['series'] = array(
                array(
                    'name' => 'Total nilai',
                    'data' => array_values($data),
                )
            );
            return $chart;
        }

        ?>
        <script>
            $(function() {
                $('#chart1').highcharts(<?= json_encode(get_chart1()) ?>);
            })
        </script>
        <div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
    <div class="panel-footer">
        <a class="btn btn-default" href="cetak.php?m=hitung" target="_blank"><span class="glyphicon glyphicon-print"></span> Cetak</a>
    </div>
</div>