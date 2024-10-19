<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "link.php"; ?>
</head>

<?php
if (isset($_POST['submit'])) {
    // Dapatkan user_id dari session atau input (sesuaikan dengan implementasi Anda)
    $user_id = $_SESSION['user_id'];
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);

    // Proses upload file
    $file_name = $_FILES['file_tugas']['name'];
    $file_tmp = $_FILES['file_tugas']['tmp_name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = uniqid() . '.' . $file_ext;
    $upload_dir = '../file_jawaban/' . $new_file_name;

    // Pindahkan file ke direktori yang diinginkan
    if (move_uploaded_file($file_tmp, $upload_dir)) {
        // Masukkan data ke database
        $query = "INSERT INTO user_submissions (user_id, task_id, file_tugas, tanggal_pengumpulan) VALUES ('$user_id', '$task_id', '$new_file_name', NOW())";
        if (mysqli_query($conn, $query)) {
            $script = "
                Swal.fire({
                    icon: 'success',
                    title: 'Tugas Berhasil Dikirim!',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            ";
        } else {
            $script = "
                Swal.fire({
                    icon: 'error',
                    title: 'Tugas Gagal Dikirim!',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            ";
        }
    } else {
        $script = "
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengupload File!',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        ";
    }
}
?>


<body id="page-top">

    <div id="wrapper">

        <?php include "sidebar.php"; ?>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <?php include "topbar.php"; ?>

                <div class="container-fluid">
                    <div class="mb-3">
                        <p>
                            <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fas fa-plus-square"></i> Kirim Tugas
                            </a>
                        </p>
                        <div class="collapse" id="collapseExample">
                            <div class="card card-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="task_id">Pilih Tugas:</label>
                                        <select class="form-control" id="task_id" name="task_id" required>
                                            <option value="">-- Pilih Tugas --</option>
                                            <?php
                                            $stmt = $conn->prepare("SELECT id, judul_tugas FROM admin_tasks WHERE deadline >= NOW()");
                                            $stmt->execute();
                                            $tasks = $stmt->get_result();
                                            foreach ($tasks as $task) {
                                                echo "<option value='{$task['id']}'>{$task['judul_tugas']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="file_tugas">Upload Jawaban (PDF/DOC):</label>
                                        <input type="file" class="form-control" id="file_tugas" name="file_tugas" accept=".pdf,.doc,.docx" required>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary w-100">Kirim</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel untuk melihat tugas yang sudah dikirim -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Jawaban Tugas yang Dikirim</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataX" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Judul Tugas</th>
                                            <th>File Jawaban</th>
                                            <th>Tanggal Pengumpulan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query untuk mengambil data tugas yang dikumpulkan oleh user ini
                                        $stmt = $conn->prepare("SELECT us.id, at.judul_tugas, us.file_tugas, us.tanggal_pengumpulan 
                                                FROM user_submissions us
                                                JOIN admin_tasks at ON us.task_id = at.id
                                                WHERE us.user_id = ?");
                                        $stmt->bind_param('i', $user_id); // Bind sesuai dengan user yang login
                                        $stmt->execute();
                                        $submissions = $stmt->get_result();
                                        $i = 1;
                                        foreach ($submissions as $submission) : ?>
                                            <tr>
                                                <td><?= $i; ?></td>
                                                <td><?= htmlspecialchars($submission['judul_tugas']); ?></td>
                                                <td><a href="../file_jawaban/<?= htmlspecialchars($submission['file_tugas']); ?>" target="_blank">Lihat Jawaban</a></td>
                                                <td><?= htmlspecialchars($submission['tanggal_pengumpulan']); ?></td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <?php include "footer.php"; ?>

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include "plugin.php"; ?>

    <script>
        $(document).ready(function() {
            $('#dataX').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Indonesian.json",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sLast": "Terakhir",
                        "sNext": "Selanjutnya",
                        "sPrevious": "Sebelumnya"
                    },
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "sSearch": "Cari:",
                    "sEmptyTable": "Tidak ada data yang tersedia dalam tabel",
                    "sLengthMenu": "Tampilkan _MENU_ data",
                    "sZeroRecords": "Tidak ada data yang cocok dengan pencarian Anda"
                }
            });
        });
    </script>

    <script>
        <?php if (isset($script)) {
            echo $script;
        } ?>
    </script>
</body>

</html>