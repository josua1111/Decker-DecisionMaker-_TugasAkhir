<?php
require_once 'functions.php';

if ($act == 'login') {
    $user = esc_field($_POST['user']);
    $pass = esc_field($_POST['pass']);

    $row = $db->get_row("SELECT * FROM tb_user WHERE user='$user' AND pass='$pass'");
    if ($row) {
        $_SESSION['login'] = $row->user;
        $_SESSION['level'] = strtolower($row->level);
        redirect_js("index.php");
    } else {
        print_msg("Salah kombinasi username dan password.");
    }
} elseif ($mod == 'password') {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    $pass3 = $_POST['pass3'];

    $row = $db->get_row("SELECT * FROM tb_user WHERE user='$_SESSION[login]' AND pass='$pass1'");

    if ($pass1 == '' || $pass2 == '' || $pass3 == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif (!$row)
        print_msg('Password lama salah.');
    elseif ($pass2 != $pass3)
        print_msg('Password baru dan konfirmasi password baru tidak sama.');
    else {
        $db->query("UPDATE tb_user SET pass='$pass2' WHERE user='$_SESSION[login]'");
        print_msg('Password berhasil diubah.', 'success');
    }
} elseif ($act == 'logout') {
    unset($_SESSION['login'], $_SESSION['level']);
    header("location:login.php");
}
/** user */
elseif ($mod == 'user_tambah') {
    $kode_user = $_POST['kode_user'];
    $nama_user = $_POST['nama_user'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $level = $_POST['level'];

    if ($kode_user == '' || $user == '' || $pass == '' || $nama_user == '' || $level == '')
        print_msg("Field yang bertanda * tidak boleh kosong!");
    elseif ($db->get_row("SELECT * FROM tb_user WHERE kode_user='$kode_user' AND kode_user<>'$_GET[ID]'")) {
        print_msg("Kode sudah ada!");
    } elseif ($db->get_row("SELECT * FROM tb_user WHERE user='$user' AND kode_user<>'$_GET[ID]'")) {
        print_msg("User sudah ada!");
    } else {
        $db->query("INSERT INTO tb_user (kode_user, user, pass, nama_user, level) 
                                    VALUES ('$kode_user', '$user', '$pass', '$nama_user', '$level')");
        redirect_js("index.php?m=user");
    }
} else if ($mod == 'user_ubah') {
    $kode_user = $_POST['kode_user'];
    $nama_user = $_POST['nama_user'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $level = $_POST['level'];

    if ($kode_user == '' || $user == '' || $pass == '' || $nama_user == '' || $level == '')
        print_msg("Field yang bertanda * tidak boleh kosong!");
    elseif ($db->get_row("SELECT * FROM tb_user WHERE user='$user' AND kode_user<>'$_GET[ID]'")) {
        print_msg("User sudah ada!");
    } else {
        $db->query("UPDATE tb_user SET 
                user='$user', 
                pass='$pass', 
                nama_user='$nama_user',                                         
                level='$level'
            WHERE kode_user='$_GET[ID]'");
        redirect_js("index.php?m=user");
    }
} else if ($act == 'user_hapus') {
    $db->query("DELETE FROM tb_user WHERE kode_user='$_GET[ID]'");
    header("location:index.php?m=user");
}
/** alternatif */
elseif ($mod == 'alternatif_tambah') {
    $kode_alternatif = $_POST['kode_alternatif'];
    $nama_alternatif = $_POST['nama_alternatif'];
    $keterangan = $_POST['keterangan'];
    if ($kode_alternatif == '' || $nama_alternatif == '')
        print_msg("Field yang bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_alternatif WHERE kode_alternatif='$kode_alternatif'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("INSERT INTO tb_alternatif (kode_alternatif, nama_alternatif, keterangan) VALUES ('$kode_alternatif', '$nama_alternatif', '$keterangan')");

        $db->query("INSERT INTO tb_alternatif_sub (kode_alternatif, kode_sub) SELECT '$kode_alternatif', kode_sub FROM tb_sub");

        foreach ($SUB as $key => $val) {
            $db->query("INSERT INTO tb_rel_alternatif(ID1, ID2, kode_sub, nilai) SELECT '$kode_alternatif', kode_alternatif, '$key', 1 FROM tb_alternatif");
            $db->query("INSERT INTO tb_rel_alternatif(ID1, ID2, kode_sub, nilai) SELECT kode_alternatif, '$kode_alternatif', '$key', 1 FROM tb_alternatif WHERE kode_alternatif<>'$kode_alternatif'");
        }

        redirect_js("index.php?m=alternatif");
    }
} else if ($mod == 'alternatif_ubah') {
    $kode_alternatif = $_POST['kode_alternatif'];
    $nama_alternatif = $_POST['nama_alternatif'];
    $keterangan = $_POST['keterangan'];
    if ($kode_alternatif == '' || $nama_alternatif == '')
        print_msg("Field yang bertanda * tidak boleh kosong!");
    else {
        $db->query("UPDATE tb_alternatif SET nama_alternatif='$nama_alternatif', keterangan='$keterangan' 
            WHERE kode_alternatif='$_GET[ID]'");
        redirect_js("index.php?m=alternatif");
    }
} else if ($act == 'alternatif_hapus') {
    $db->query("DELETE FROM tb_alternatif WHERE kode_alternatif='$_GET[ID]'");
    hapus_data();
    header("location:index.php?m=alternatif");
}

/** kriteria */
elseif ($mod == 'kriteria_tambah') {
    $kode_kriteria = $_POST['kode_kriteria'];
    $nama_kriteria = $_POST['nama_kriteria'];

    if ($kode_kriteria == '' || $nama_kriteria == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_kriteria WHERE kode_kriteria='$kode_kriteria'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("INSERT INTO tb_kriteria (kode_kriteria, nama_kriteria) 
            VALUES ('$kode_kriteria', '$nama_kriteria')");
        $db->query("INSERT INTO tb_rel_kriteria(ID1, ID2, nilai) 
            SELECT '$kode_kriteria', kode_kriteria, 1 FROM tb_kriteria");
        $db->query("INSERT INTO tb_rel_kriteria(ID1, ID2, nilai) 
            SELECT kode_kriteria, '$kode_kriteria', 1 FROM tb_kriteria WHERE kode_kriteria<>'$kode_kriteria'");
        redirect_js("index.php?m=kriteria");
    }
} else if ($mod == 'kriteria_ubah') {
    $kode_kriteria = $_POST['kode_kriteria'];
    $nama_kriteria = $_POST['nama_kriteria'];

    if ($kode_kriteria == '' || $nama_kriteria == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_kriteria WHERE kode_kriteria='$kode_kriteria' AND kode_kriteria<>'$_GET[ID]'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("UPDATE tb_kriteria SET kode_kriteria='$kode_kriteria', nama_kriteria='$nama_kriteria' WHERE kode_kriteria='$_GET[ID]'");
        redirect_js("index.php?m=kriteria");
    }
} else if ($act == 'kriteria_hapus') {
    $db->query("DELETE FROM tb_kriteria WHERE kode_kriteria='$_GET[ID]'");
    hapus_data();
    header("location:index.php?m=kriteria");
}
/** sub */
elseif ($mod == 'sub_tambah') {
    $kode_sub = $_POST['kode_sub'];
    $kode_kriteria = $_POST['kode_kriteria'];
    $nama_sub = $_POST['nama_sub'];

    if ($kode_sub == '' || $kode_kriteria == '' || $nama_sub == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_sub WHERE kode_sub='$kode_sub'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("INSERT INTO tb_sub (kode_sub, kode_kriteria, nama_sub) 
            VALUES ('$kode_sub', '$kode_kriteria', '$nama_sub')");
        $db->query("INSERT INTO tb_rel_sub(ID1, ID2, nilai) 
            SELECT '$kode_sub', kode_sub, 1 FROM tb_sub");
        $db->query("INSERT INTO tb_rel_sub(ID1, ID2, nilai) 
            SELECT kode_sub, '$kode_sub', 1 FROM tb_sub WHERE kode_sub<>'$kode_sub'");

        $db->query("INSERT INTO tb_rel_alternatif (kode_sub, ID1, ID2, nilai) SELECT '$kode_sub', a1.kode_alternatif, a2.kode_alternatif, 1 FROM tb_alternatif a1, tb_alternatif a2");

        $db->query("INSERT INTO tb_alternatif_sub (kode_alternatif, kode_sub) SELECT kode_alternatif, '$kode_sub' FROM tb_alternatif");

        redirect_js("index.php?m=sub");
    }
} else if ($mod == 'sub_ubah') {
    $kode_sub = $_POST['kode_sub'];
    $kode_kriteria = $_POST['kode_kriteria'];
    $nama_sub = $_POST['nama_sub'];

    if ($kode_sub == '' || $kode_kriteria == '' || $nama_sub == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_sub WHERE kode_sub='$kode_sub' AND kode_sub<>'$_GET[ID]'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("UPDATE tb_sub SET kode_sub='$kode_sub', kode_kriteria='$kode_kriteria', nama_sub='$nama_sub' WHERE kode_sub='$_GET[ID]'");
        redirect_js("index.php?m=sub");
    }
} else if ($act == 'sub_hapus') {
    $db->query("DELETE FROM tb_sub WHERE kode_sub='$_GET[ID]'");
    hapus_data();
    header("location:index.php?m=sub");
}

/** rel_kriteria */
else if ($mod == 'rel_kriteria') {
    $ID1 = $_POST['ID1'];
    $ID2 = $_POST['ID2'];
    $nilai = abs($_POST['nilai']);

    if ($ID1 == $ID2 && $nilai <> 1)
        print_msg("Kriteria yang sama harus bernilai 1.");
    else {
        $db->query("UPDATE tb_rel_kriteria SET nilai=$nilai WHERE ID1='$ID1' AND ID2='$ID2'");
        $db->query("UPDATE tb_rel_kriteria SET nilai=1/$nilai WHERE ID2='$ID1' AND ID1='$ID2'");
        print_msg("Nilai kriteria berhasil diubah.", 'success');
    }
}

/** rel_sub */
else if ($mod == 'rel_sub') {
    $ID1 = $_POST['ID1'];
    $ID2 = $_POST['ID2'];
    $nilai = abs($_POST['nilai']);

    if ($ID1 == $ID2 && $nilai <> 1)
        print_msg("Sub yang sama harus bernilai 1.");
    else {
        $db->query("UPDATE tb_rel_sub SET nilai=$nilai WHERE ID1='$ID1' AND ID2='$ID2'");
        $db->query("UPDATE tb_rel_sub SET nilai=1/$nilai WHERE ID2='$ID1' AND ID1='$ID2'");
        print_msg("Nilai sub berhasil diubah.", 'success');
    }
}

/** rel_alternatif */
else if ($mod == 'rel_alternatif') {
    $kode_sub = $_GET['kode_sub'];
    $ID1 = $_POST['ID1'];
    $ID2 = $_POST['ID2'];
    $nilai = abs($_POST['nilai']);

    if ($ID1 == $ID2 && $nilai <> 1)
        print_msg("Alternatif yang sama harus bernilai 1.");
    else {
        $db->query("UPDATE tb_rel_alternatif SET nilai=$nilai WHERE ID1='$ID1' AND ID2='$ID2' AND kode_sub='$kode_sub'");
        $db->query("UPDATE tb_rel_alternatif SET nilai=1/$nilai WHERE ID2='$ID1' AND ID1='$ID2' AND kode_sub='$kode_sub'");
        print_msg("Nilai alternatif berhasil diubah.", 'success');
    }
}

function hapus_data()
{
    global $db;
    // tb_sub
    $db->query("DELETE FROM tb_sub WHERE kode_kriteria NOT IN (SELECT kode_kriteria FROM tb_kriteria)");
    // tb_alternatif_sub
    $db->query("DELETE FROM tb_alternatif_sub WHERE kode_alternatif NOT IN (SELECT kode_alternatif FROM tb_alternatif)");
    $db->query("DELETE FROM tb_alternatif_sub WHERE kode_sub NOT IN (SELECT kode_sub FROM tb_sub)");
    // tb_rel_alternatif
    $db->query("DELETE FROM tb_rel_alternatif WHERE kode_sub NOT IN (SELECT kode_sub FROM tb_sub)");
    $db->query("DELETE FROM tb_rel_alternatif WHERE ID1 NOT IN (SELECT kode_alternatif FROM tb_alternatif)");
    $db->query("DELETE FROM tb_rel_alternatif WHERE ID2 NOT IN (SELECT kode_alternatif FROM tb_alternatif)");
    // tb_rel_kriteria
    $db->query("DELETE FROM tb_rel_kriteria WHERE ID1 NOT IN (SELECT kode_kriteria FROM tb_kriteria)");
    $db->query("DELETE FROM tb_rel_kriteria WHERE ID2 NOT IN (SELECT kode_kriteria FROM tb_kriteria)");
    // tb_rel_sub
    $db->query("DELETE FROM tb_rel_sub WHERE ID1 NOT IN (SELECT kode_sub FROM tb_sub)");
    $db->query("DELETE FROM tb_rel_sub WHERE ID2 NOT IN (SELECT kode_sub FROM tb_sub)");
}
