<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 31/01/2018
 * Time: 15.20
 */

require '../../config/api.php';
$admin = new Admin();

if(@$_GET['type'] == 'generate'){

    $kode = $_POST['kodeGenerate'];
    $spk = $_POST['spk'];
    $sql = "UPDATE tb_kerjasama_perusahan SET kode_list_karyawan = :kode WHERE nomor_kontrak = :nomor ";
    $stmt = $admin->runQuery($sql);
    $stmt->execute(array(
        ":kode" => $kode,
        ":nomor" => $spk
    ));

    if ($stmt){
        echo "Generate Kode Berhasil!";
    }else{
        echo "Generate tidak Berhasil.";
    }
}

elseif(@$_GET['type'] == 'addKaryawan'){

    $kdList = $_POST['spk'];
    $ktp = $_POST['ktp'];
    $status = '1';
    $karyawanStatus="KDKRY0002";

    $sql = "INSERT INTO tb_list_karyawan (kode_list_karyawan, no_nip, status_karyawan) VALUES (:kode, :id, :status)";
    $stmt = $admin->runQuery($sql);
    $stmt->execute(array(
        ':kode' => $kdList,
        ':id'   => $ktp,
        ':status' => $status
    ));


        if($stmt){
            echo "Karyawan Berhasil Ditambahkan.";
            $query = "UPDATE tb_karyawan SET kd_status_karyawan = :status WHERE no_ktp = :data";

            $update = $admin->runQuery($query);
            $update->execute(array(
                ':status'   => $karyawanStatus,
                ':data'     => $ktp
            ));

        }else{
            echo "Gagal ditambahkan";
        }

//    statuskaryawan = suggest_karyawan : 1
}
elseif(@$_GET['type'] == 'addKaryawanMPO'){

    $kdList = $_POST['spk'];
    $ktp = $_POST['ktp'];
    $posisi = $_POST['posisi'];
    $status = '1';
    $karyawanStatus="KDKRY0002";

    $sql = "INSERT INTO tb_list_karyawan (kode_list_karyawan, no_nip, kode_pekerjaan, status_karyawan) VALUES (:kode, :id, :pekerjaan, :status)";
    $stmt = $admin->runQuery($sql);
    $stmt->execute(array(
        ':kode' => $kdList,
        ':id'   => $ktp,
        ':pekerjaan' => $posisi,
        ':status' => $status
    ));


    if($stmt){
        echo "Karyawan Berhasil Ditambahkan.";
        $query = "UPDATE tb_karyawan SET kd_status_karyawan = :status WHERE no_ktp = :data";

        $update = $admin->runQuery($query);
        $update->execute(array(
            ':status'   => $karyawanStatus,
            ':data'     => $ktp
        ));

    }else{
        echo "Gagal ditambahkan";
    }

//    statuskaryawan = suggest_karyawan : 1
}
elseif (@$_GET['type'] == 'removeKaryawan'){
    $kode = $_POST['kode'];
    $ktp = $_POST['ktp'];
    $karyawanStatus = "KDKRY0008";

    $sql = "DELETE FROM tb_list_karyawan WHERE no_nip=:id AND kode_list_karyawan=:kode";
    $stmt = $admin->runQuery($sql);
    $stmt->execute(array(
        ':id'	=>$ktp,
        ':kode' =>$kode
    ));
    if (!$stmt) {
        # code...
        echo "Data Tidak berhasil di hapus.";
    }else{
        echo "Karyawan Berhasil di Hapus!";

        $query = "UPDATE tb_karyawan SET kd_status_karyawan = :status WHERE no_ktp = :data";

        $update = $admin->runQuery($query);
        $update->execute(array(
            ':status'   => $karyawanStatus,
            ':data'     => $ktp
        ));


        //insert into tb_temp_remove karyawan
        $sql = "INSERT INTO tb_temp_remove_karyawan (kode_list_karyawan, no_ktp) VALUES (:kode, :ktp)";

        $insert = $admin->runQuery($sql);
        $insert->execute(array(
            ':kode'   => $kode,
            ':ktp'     => $ktp
        ));
    }
}
elseif (@$_GET['type'] == 'finishAdd'){

    $kode = $_POST['kode'];

    //cek data jika kurang/lebih karyawan

    $cek = "SELECT tb_kerjasama_perusahan.nomor_kontrak, tb_kerjasama_perusahan.kode_perusahaan, tb_kerjasama_perusahan.kode_request, tb_kerjasama_perusahan.kode_list_karyawan, tb_kerjasama_perusahan.total_karyawan
            FROM tb_kerjasama_perusahan WHERE tb_kerjasama_perusahan.nomor_kontrak = :spk";
    $cekdata = $admin->runQuery($cek);
    $cekdata->execute(array(
        ':spk'  => $kode
    ));

    $data = $cekdata->fetch(PDO::FETCH_LAZY);

    $nomorReq = $data['kode_request'];

    $totalKaryawan = $data['total_karyawan'];
        $sql = "SELECT id FROM tb_list_karyawan WHERE kode_list_karyawan = :kode";
        $stmt = $admin->runQuery($sql);
        $stmt->execute(array(
            ':kode' => $data['kode_list_karyawan']
        ));

        $totalList = $stmt->rowCount();

        $row = $stmt->fetch(PDO::FETCH_LAZY);

    if($totalList > $totalKaryawan OR $totalList < $totalKaryawan ){
        echo "0";
    }else{
        $ubah = "UPDATE tb_temporary_perusahaan SET status = :a WHERE no_pendaftaran = :req";
        $change = $admin->runQuery($ubah);
        $change->execute(array(
            ':a' => '4',
            ':req' => $nomorReq
        ));
        echo "1";
    }



}
elseif(@$_GET['type'] == 'addRating'){
    $id = $_POST['id'];
    $star = $_POST['star'];
    $spv = $_POST['admin'];

    $sql = "UPDATE tb_report_job SET rating = :rating, spv_id = :admin WHERE id = :id";
    $stmt = $admin->runQuery($sql);
    $stmt->execute(array(
        ':rating' => $star,
        ':admin'  => $spv,
        ':id'     => $id
    ));

    if($stmt){
        echo "Jobs Telah di update";
    }else{
        echo "Gagal!";
    }
}
elseif(@$_GET['type'] == 'changeStatus'){
    $ktp = $_POST['ktp'];
    $status = $_POST['st'];

    $query = "UPDATE tb_list_karyawan SET status_karyawan = :status WHERE no_nip = :data";

    $update = $admin->runQuery($query);
    $update->execute(array(
        ':status'   => $status,
        ':data'     => $ktp
    ));

    if($update){

        if($status == '2'){
            $kd = "KDKRY0011"; //approve to project
            $sql = "UPDATE tb_karyawan SET kd_status_karyawan = :status WHERE no_ktp = :st";
            $stmt = $admin->runQuery($sql);
            $stmt->execute(array(
                ':status' => $kd,
                ':st'     => $ktp
            ));
            if($stmt){
                echo "Success";
            }else{
                echo "Gagal";
            }
        }elseif( $status == '3'){
            $kd = "KDKRY0012"; //decline to project
            $sql = "UPDATE tb_karyawan SET kd_status_karyawan = :status WHERE no_ktp = :st";
            $stmt = $admin->runQuery($sql);
            $stmt->execute(array(
                ':status' => $kd,
                ':st'     => $ktp
            ));
            if($stmt){
                echo "Success";
            }else{
                echo "Gagal";
            }
        }else{

            $kd = "KDKRY0002"; //assign to project
            $sql = "UPDATE tb_karyawan SET kd_status_karyawan = :status WHERE no_ktp = :st";
            $stmt = $admin->runQuery($sql);
            $stmt->execute(array(
                ':status' => $kd,
                ':st'     => $ktp
            ));
            if($stmt){
                echo "Success";
            }else{
                echo "Gagal";
            }

        }


    }else{
        echo "Failed";
    }


}
elseif(@$_GET['type'] == 'generateInvoice'){
    $kontrak = $_POST['kontrak'];

    $sql = "SELECT * FROM tb_kerjasama_perusahan WHERE nomor_kontrak = :kontrak";
    $stmt = $admin->runQuery($sql);
    $stmt->execute(array(
        ':kontrak'  => $kontrak
    ));
    $info = $stmt->fetch(PDO::FETCH_LAZY);

    $type = substr($info['kode_request'], 0, 3); //cek for MPO or Others

    if($type == 'MPO'){
        $totalTerm = $info['total'] / 30; //total term pembayaran berapa kali
        $totalTerm = round($totalTerm, 0, 1); //dibulatkan ke terkecil

        $kodeList = $info['kode_plan']; //kode tb_list_perkerjaan_perusahaan

        if($info['nilai_kontrak'] == '0'){
            //cek total gaji
            $sql = "SELECT SUM(total * gaji) AS totalgaji FROM tb_list_perkerjaan_perusahaan WHERE code = :code";
            $stmt = $admin->runQuery($sql);
            $stmt->execute(array(':code' => $kodeList));
            $total = $stmt->fetch(PDO::FETCH_LAZY);
            $totalGaji = $total['totalgaji']; //total gaji semua karyawan

            $subTotal = $totalGaji / 30; //gaji per-hari;
            $subTotal = round($subTotal, 0, 3);

            $totalNilaiProject = $subTotal * $info['total']; //nilai project MPO
            $totalNilaiTerm = $totalNilaiProject / $totalTerm;

            $query = "UPDATE tb_kerjasama_perusahan SET nilai_kontrak = :nilai WHERE nomor_kontrak = :kontrak";
            $input = $admin->runQuery($query);
            $input->execute(array(
                ':nilai'    => $totalNilaiProject,
                ':kontrak'  => $kontrak
            ));
            if($input){

            echo "Try Again!.";

            }else{
                echo 'gagal';
            }


        }else{
//           $totalTerm = $info['total'] / 30; //total term pembayaran berapa kali

            $totalTerm = round($totalTerm, 0, 1); //dibulatkan ke terkecil

            $kontrakStart = date('Y/m/d', strtotime($info['kontrak_start'])); //get start kontrak

            $nominal = $info['nilai_kontrak']; //total nomila project

            $nominal = $nominal / $totalTerm;
            $nominal = round($nominal, 0,1);

            $termDate = date('Y/m/d', strtotime($kontrakStart. '+ 30 days'));

            // echo 'startkontra: '.$kontrakStart. ' Due date ke 1: ' .$termDate;

            for ($i = 1; $i <= $totalTerm; $i++){
                $query = "SELECT * FROM tb_term_pembayaran WHERE kode_term = :kode";
                $cek = $admin->runQuery($query);
                $cek->execute(array(':kode' => $kontrak));


                if($cek->rowCount() > 0){
                    $query = "SELECT MAX(id) AS id FROM tb_term_pembayaran WHERE kode_term = :kode";
                    $cek = $admin->runQuery($query);
                    $cek->execute(array(':kode' => $kontrak));

                    $info = $cek->fetch(PDO::FETCH_LAZY);

                    $lastID = $info['id'];

                    $sql = "SELECT due_date FROM tb_term_pembayaran WHERE id = :id";
                    $cari = $admin->runQuery($sql);
                    $cari->execute(array(':id'  => $lastID));

                    $hasil = $cari->fetch(PDO::FETCH_LAZY);

                    $tanggal = date('Y/m/d', strtotime($hasil['due_date']));

                    $pembayaran = date('Y/m/d', strtotime($tanggal. '+ 30 days'));

//            echo 'last id: '.$lastID. ' pembayaran: '.$tanggal;
//            echo 'yes rowCount: '.$cek->rowCount();
                    $sql = "INSERT INTO tb_term_pembayaran (kode_term, nama_term, due_date, keterangan) VALUES (:a, :b, :c, :d)";
                    $stmt = $admin->runQuery($sql);
                    $stmt->execute(array(
                        ':a'    => $kontrak,
                        ':b'    => 'Pembayaran Ke-'.$i,
                        ':c'    => $pembayaran,
                        'd'     =>$nominal
                    ));

                    if($stmt){

                    }else{
                        echo 'Failed';
                    }
                }else{
                    $pembayaran = $termDate;
//            echo $pembayaran;
//            echo 'no';
                    $sql = "INSERT INTO tb_term_pembayaran (kode_term, nama_term, due_date, keterangan) VALUES (:a, :b, :c, :d)";
                    $stmt = $admin->runQuery($sql);
                    $stmt->execute(array(
                        ':a'    => $kontrak,
                        ':b'    => 'Pembayaran Ke-'.$i,
                        ':c'    => $pembayaran,
                        'd'     => $nominal
                    ));
                }

            }
            echo "Success.";
        }

    }else{
        $totalTerm = $info['total'] / 30; //total term pembayaran berapa kali

        $totalTerm = round($totalTerm, 0, 1); //dibulatkan ke terkecil

        $kontrakStart = date('Y/m/d', strtotime($info['kontrak_start'])); //get start kontrak

        $nominal = $info['nilai_kontrak']; //total nomila project
        $nominal = $nominal / $totalTerm;
        $nominal = round($nominal, 0,1);

//    echo 'start: '.$kontrakStart.' ends: '.$kontrakEnd;

        $termDate = date('Y/m/d', strtotime($kontrakStart. '+ 30 days'));


        for ($i = 1; $i <= $totalTerm; $i++){
            $query = "SELECT * FROM tb_term_pembayaran WHERE kode_term = :kode";
            $cek = $admin->runQuery($query);
            $cek->execute(array(':kode' => $kontrak));


            if($cek->rowCount() > 0){
                $query = "SELECT MAX(id) AS id FROM tb_term_pembayaran WHERE kode_term = :kode";
                $cek = $admin->runQuery($query);
                $cek->execute(array(':kode' => $kontrak));

                $info = $cek->fetch(PDO::FETCH_LAZY);

                $lastID = $info['id'];

                $sql = "SELECT due_date FROM tb_term_pembayaran WHERE id = :id";
                $cari = $admin->runQuery($sql);
                $cari->execute(array(':id'  => $lastID));

                $hasil = $cari->fetch(PDO::FETCH_LAZY);

                $tanggal = date('Y/m/d', strtotime($hasil['due_date']));

                $pembayaran = date('Y/m/d', strtotime($tanggal. '+ 30 days'));

//            echo 'last id: '.$lastID. ' pembayaran: '.$tanggal;
//            echo 'yes rowCount: '.$cek->rowCount();
                $sql = "INSERT INTO tb_term_pembayaran (kode_term, nama_term, due_date, keterangan) VALUES (:a, :b, :c, :d)";
                $stmt = $admin->runQuery($sql);
                $stmt->execute(array(
                    ':a'    => $kontrak,
                    ':b'    => 'Pembayaran Ke-'.$i,
                    ':c'    => $pembayaran,
                    'd'     =>$nominal
                ));

                if($stmt){

                }else{
                    echo 'Failed';
                }
            }else{
                $pembayaran = $termDate;
//            echo $pembayaran;
//            echo 'no';
                $sql = "INSERT INTO tb_term_pembayaran (kode_term, nama_term, due_date, keterangan) VALUES (:a, :b, :c, :d)";
                $stmt = $admin->runQuery($sql);
                $stmt->execute(array(
                    ':a'    => $kontrak,
                    ':b'    => 'Pembayaran Ke-'.$i,
                    ':c'    => $pembayaran,
                    'd'     => $nominal
                ));
            }

        }

        echo "Success";
    }


}


else{
    echo "none";
}