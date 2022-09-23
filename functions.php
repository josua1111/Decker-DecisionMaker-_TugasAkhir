<?php
error_reporting(~E_NOTICE);
session_start();

include 'config.php';
include 'includes/db.php';
$db = new DB($config['server'], $config['username'], $config['password'], $config['database_name']);
include 'includes/ahp_smart.php';

$mod = $_GET['m'];
$act = $_GET['act'];

$nRI = array(
    1 => 0,
    2 => 0,
    3 => 0.58,
    4 => 0.9,
    5 => 1.12,
    6 => 1.24,
    7 => 1.32,
    8 => 1.41,
    9 => 1.46,
    10 => 1.49,
    11 => 1.51,
    12 => 1.48,
    13 => 1.56,
    14 => 1.57,
    15 => 1.59
);

$rows = $db->get_results("SELECT * FROM tb_alternatif ORDER BY kode_alternatif");
foreach ($rows as $row) {
    $ALTERNATIF[$row->kode_alternatif] = $row;
}

$rows = $db->get_results("SELECT * FROM tb_kriteria ORDER BY kode_kriteria");
foreach ($rows as $row) {
    $KRITERIA[$row->kode_kriteria] = $row;
}

$rows = $db->get_results("SELECT * FROM tb_sub ORDER BY kode_sub");
foreach ($rows as $row) {
    $KRITERIA_SUB[$row->kode_kriteria][$row->kode_sub] = $row;
    $SUB[$row->kode_sub] = $row;
}

function get_rel_kriteria()
{
    global $db;
    $arr = array();
    $rows = $db->get_results("SELECT * FROM tb_rel_kriteria ORDER BY ID1, ID2");
    foreach ($rows as $row) {
        $arr[$row->ID1][$row->ID2] = $row->nilai;
    }
    return $arr;
}
function get_alternatif_sub()
{
    global $db;
    $arr = array();
    $rows = $db->get_results("SELECT * FROM tb_alternatif_sub ORDER BY kode_alternatif, kode_sub");
    foreach ($rows as $row) {
        $arr[$row->kode_alternatif][$row->kode_sub] = $row->bobot_alternatif;
    }
    return $arr;
}

function get_rel_sub($kode_kriteria)
{
    global $db;
    $arr = array();
    $rows = $db->get_results("SELECT * FROM tb_rel_sub r WHERE r.ID1 IN (SELECT kode_sub FROM tb_sub WHERE kode_kriteria='$kode_kriteria') AND r.ID2 IN (SELECT kode_sub FROM tb_sub WHERE kode_kriteria='$kode_kriteria') ORDER BY ID1, ID2");

    foreach ($rows as $row) {
        $arr[$row->ID1][$row->ID2] = $row->nilai;
    }
    return $arr;
}

function get_kriteria_option($selected)
{
    global $KRITERIA;
    $a = '';
    foreach ($KRITERIA as $key => $val) {
        if ($key == $selected)
            $a .= "<option value='$key' selected>$key - $val->nama_kriteria</option>";
        else
            $a .= "<option value='$key'>$key - $val->nama_kriteria</option>";
    }
    return $a;
}

function get_alternatif_option($selected)
{
    global $ALTERNATIF;
    $a = '';
    foreach ($ALTERNATIF as $key => $val) {
        if ($key == $selected)
            $a .= "<option value='$key' selected>$key - $val->nama_alternatif</option>";
        else
            $a .= "<option value='$key'>$key - $val->nama_alternatif</option>";
    }
    return $a;
}

function get_sub_option($kode_kriteria, $selected)
{
    global $KRITERIA_SUB;
    $a = '';
    foreach ($KRITERIA_SUB[$kode_kriteria] as $key => $val) {
        if ($key == $selected)
            $a .= "<option value='$key' selected>$key - $val->nama_sub</option>";
        else
            $a .= "<option value='$key'>$key - $val->nama_sub</option>";
    }
    return $a;
}

function get_sub_all_option($selected)
{
    global $SUB;
    $a = '';
    foreach ($SUB as $key => $val) {
        if ($key == $selected)
            $a .= "<option value='$key' selected>$key - $val->nama_sub</option>";
        else
            $a .= "<option value='$key'>$key - $val->nama_sub</option>";
    }
    return $a;
}

function get_nilai_option($selected = '')
{
    $nilai = array(
        '1' => 'Sama penting dengan',
        '2' => 'Mendekati sedikit lebih penting dari',
        '3' => 'Sedikit lebih penting dari',
        '4' => 'Mendekati lebih penting dari',
        '5' => 'Lebih penting dari',
        '6' => 'Mendekati sangat penting dari',
        '7' => 'Sangat penting dari',
        '8' => 'Mendekati mutlak dari',
        '9' => 'Mutlak sangat penting dari',
    );
    $a = '';
    foreach ($nilai as $key => $val) {
        if ($selected == $key)
            $a .= "<option value='$key' selected>$key - $val</option>";
        else
            $a .= "<option value='$key'>$key - $val</option>";
    }
    return $a;
}

function get_rel_alternatif($kode_sub)
{
    global $db;
    $arr = array();
    $rows = $db->get_results("SELECT * FROM tb_rel_alternatif WHERE kode_sub='$kode_sub' ORDER BY ID1, ID2");

    foreach ($rows as $row) {
        $arr[$row->ID1][$row->ID2] = $row->nilai;
    }
    return $arr;
}

/**
 * Membuat opsi level
 *
 * @param       string  $selected   Level terpilih 
 * @return      string  
 */
function get_level_option($selected = '')
{
    $arr = array(
        'admin' => 'Admin',
        'pengawas' => 'Pengawas',
    );
    $a = '';
    foreach ($arr as $key => $val) {
        if ($selected == $key)
            $a .= "<option value='$key' selected>$val</option>";
        else
            $a .= "<option value='$key'>$val</option>";
    }
    return $a;
}

function kode_oto($field, $table, $prefix, $length)
{
    global $db;
    $var = $db->get_var("SELECT $field FROM $table WHERE $field REGEXP '{$prefix}[0-9]{{$length}}' ORDER BY $field DESC");
    if ($var) {
        return $prefix . substr(str_repeat('0', $length) . ((int) substr($var, -$length) + 1), -$length);
    } else {
        return $prefix . str_repeat('0', $length - 1) . 1;
    }
}

/**
 * Menampilkan value dari variabel POST atau GET
 * @param string $key nama field atau variabel
 * @param string $default data asli jika null
 * @return string Isi variabel POST atau get
 */
function set_value($key = null, $default = null)
{
    global $_POST;
    if (isset($_POST[$key]))
        return $_POST[$key];

    if (isset($_GET[$key]))
        return $_GET[$key];

    return $default;
}
function esc_field($str)
{
    return addslashes($str);
}

function redirect_js($url)
{
    echo '<script type="text/javascript">window.location.replace("' . $url . '");</script>';
}

function alert($url)
{
    echo '<script type="text/javascript">alert("' . $url . '");</script>';
}

function print_msg($msg, $type = 'danger')
{
    echo ('<div class="alert alert-' . $type . ' alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $msg . '</div>');
}
