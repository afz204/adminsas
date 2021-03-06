<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 07/02/2018
 * Time: 02.04
 */

include_once '../../config/api.php';

$config = new Admin();

$kupon = $_GET['kupon'];
$kupon2 = $_GET['kupon'];

$admin_id = $_GET['admin'];

$sql = "SELECT * FROM tb_complain_perusahaan WHERE kode_komplain = :kode";
$stmt = $config->runQuery($sql);
$stmt->execute(array(
    ':kode' => $kupon
));
$isi = $stmt->fetch(PDO::FETCH_LAZY);

$query = "SELECT * FROM tb_complain_perusahaan WHERE id_reff = :kode ORDER BY update_on DESC";
$cek = $config->runQuery($query);
$cek->execute(array(
    ':kode' => $kupon2
));


$replay = "SELECT tb_complain_perusahaan.id, tb_complain_perusahaan.id_reff, tb_complain_perusahaan.admin FROM tb_complain_perusahaan
WHERE tb_complain_perusahaan.admin = :kodePerusahaan AND tb_complain_perusahaan.id_reff = :kodeKomplain AND tb_complain_perusahaan.status = '' 
ORDER BY tb_complain_perusahaan.update_on DESC";
$id = $config->runQuery($replay);
$id->execute(array(
        ':kodePerusahaan'   => $isi['kode_perusahaan'],
        ':kodeKomplain'     => $kupon2
));

$id = $id->fetch(PDO::FETCH_LAZY);
echo $id['id'];

?>

<div id="contentComplain">
    <div class="x_title">
        <h2>Detail Komplain Kupon : <label class="label label-lg label-primary"><?=$kupon?></label> </h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" id="timelineComplain">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_content">
                <div class="jumbotron">
                    <h3><?=$isi['judul']?></h3>
                    <p style="margin-bottom: unset !important; font-size: 16px !important;"><?=$isi['isi']?></p>
                </div>
                <ul class="list-unstyled timeline">
                    <?php

                    if ($cek->rowCount() > 0){
                        while ($row = $cek->fetch(PDO::FETCH_LAZY)){

                            $tgl = $row['update_on'];
                            $tanggal = date( "m/d/y", strtotime($tgl));

                            $menit = date("H:m:s a", strtotime($tgl));
                            ?>
                            <li>
                                <div class="block">
                                    <div class="tags">
                                        <a href="#" class="tag">
                                            <span ><?=$tanggal?></span>
                                        </a>
                                    </div>
                                    <div class="block_content">
                                        <h2 class="title">
                                            <a><?=$row['judul']?></a>
                                        </h2>
                                        <div class="byline">
                                            <span><?=$menit?></span> by <a><?=$row['admin']?></a>
                                        </div>
                                        <p class="excerpt"> <?=$row['isi']?>
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <br>

                        <?php }
                        echo '<button type="button" class="btn btn-primary formBalasComplain" data-kupon="'.$kupon.'" data-id="'.$id['id'].'" data-admin='.$admin_id.'"  data-toggle="modal" data-target=".balasComplain"><span class="fa fa-reply"></span> Kirim Balasan</button>
';
                    }else{ ?>
                        <li>
                            <div class="jumbotron">
                                <p> Belum Ada Activity!</p>
                            </div>
                        </li>
                        <button type="button" class="btn btn-primary formBalasComplain" data-kupon="<?=$kupon?>" data-admin="<?=$admin_id?>" data-id="<?=$id['id']?>" data-toggle="modal" data-target=".balasComplain"><span class="fa fa-reply"></span> Kirim Balasan</button>

                    <?php }

                    ?>

                </ul>

            </div>
        </div>

    </div>
</div>
