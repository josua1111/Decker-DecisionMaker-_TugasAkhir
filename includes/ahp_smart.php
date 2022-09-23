<?php
class AHP
{
    function __construct($data)
    {
        $this->data = $data;
        $this->baris_total();
        $this->normal();
        $this->prioritas();
        $this->cm();
    }
    function baris_total()
    {
        $this->baris_total = array();
        foreach ($this->data as $key => $val) {
            foreach ($val as $k => $v) {
                $this->baris_total[$k] += $v;
            }
        }
    }
    function normal()
    {
        $this->normal = array();
        foreach ($this->data as $key => $val) {
            foreach ($val as $k => $v) {
                $this->normal[$key][$k] = $v / $this->baris_total[$k];
            }
        }
    }
    function prioritas()
    {
        $this->prioritas = array();
        foreach ($this->normal as $key => $val) {
            $this->prioritas[$key] = array_sum($val) / count($val);
        }
    }
    function cm()
    {
        $this->cm = array();
        foreach ($this->data as $key => $val) {
            foreach ($val as $k => $v) {
                $this->cm[$key] += $v * $this->prioritas[$k];
            }
            $this->cm[$key] /= $this->prioritas[$key];
        }
    }
}

function get_sub_bobot()
{
    global $KRITERIA, $SUB;
    foreach ($SUB as $key => $val) {
        $arr[$key] = $val->bobot_sub * $KRITERIA[$val->kode_kriteria]->bobot_kriteria;
    }
    return $arr;
}

function get_terbobot($alternatif_sub, $sub_bobot)
{
    foreach ($alternatif_sub as $key => $val) {
        foreach ($val as $k => $v) {
            $arr[$key][$k] = $v * $sub_bobot[$k];
        }
    }
    return $arr;
}
function get_total($terbobot)
{
    foreach ($terbobot as $key => $val) {
        $arr[$key] = array_sum($val);
    }
    return $arr;
}

function get_rank($array)
{
    $data = $array;
    arsort($data);
    $no = 1;
    $new = array();
    foreach ($data as $key => $value) {
        $new[$key] = $no++;
    }
    return $new;
}
