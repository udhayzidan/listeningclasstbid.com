<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "link.php"; ?>
</head>

<?php
if (isset($_POST['submit'])) {
    $judul_tugas = mysqli_real_escape_string($conn, $_POST['judul_tugas']);
    $deskripsi_tugas = mysqli_real_escape_string($conn, $_POST['deskripsi_tugas']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $admin_id = 1;

    $query = "INSERT INTO admin_tasks (admin_id, judul_tugas, deskripsi_tugas, deadline) VALUES ('$admin_id', '$judul_tugas', '$deskripsi_tugas', '$deadline')";
    if (mysqli_query($conn, $query)) {
        $script = "
            Swal.fire({
                icon: 'success',
                title: 'Tugas Berhasil Ditambahkan!',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        ";
    } else {
        $script = "
            Swal.fire({
                icon: 'error',
                title: 'Tugas Gagal Ditambahkan!',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        ";
    }
}

if (isset($_POST['edit'])) {
    $id_tugas = mysqli_real_escape_string($conn, $_POST['id_tugas']);
    $judul_tugas = mysqli_real_escape_string($conn, $_POST['judul_tugas']);
    $deskripsi_tugas = mysqli_real_escape_string($conn, $_POST['deskripsi_tugas']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

    $query = "UPDATE admin_tasks SET judul_tugas = '$judul_tugas', deskripsi_tugas = '$deskripsi_tugas', deadline = '$deadline' WHERE id = '$id_tugas'";

    if (mysqli_query($conn, $query)) {
        $script = "
            Swal.fire({
                icon: 'success',
                title: 'Tugas Berhasil Di-Edit!',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        ";
    } else {
        $script = "
            Swal.fire({
                icon: 'error',
                title: 'Tugas Gagal Di-Edit!',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        ";
    }
}

if (isset($_POST['hapus'])) {
    $id_tugas = mysqli_real_escape_string($conn, $_POST['id_tugas']);

    $query = "DELETE FROM admin_tasks WHERE id = '$id_tugas'";
    if (mysqli_query($conn, $query)) {
        $script = "
            Swal.fire({
                icon: 'success',
                title: 'Tugas Berhasil Dihapus!',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        ";
    } else {
        $script = "
            Swal.fire({
                icon: 'error',
                title: 'Tugas Gagal Dihapus!',
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
                                <i class="fas fa-plus-square"></i> Tambah Tugas
                            </a>
                        </p>
                        <div class="collapse" id="collapseExample">
                            <div class="card card-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="judul_tugas">Judul Tugas:</label>
                                        <input type="text" class="form-control" id="judul_tugas" name="judul_tugas" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="deskripsi_tugas">Deskripsi Tugas:</label>
                                        <textarea class="form-control" id="deskripsi_tugas" name="deskripsi_tugas" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="deadline">Deadline:</label>
                                        <input type="datetime-local" class="form-control" id="deadline" name="deadline" required>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Tugas</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataX" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Judul Tugas</th>
                                            <th>Deskripsi Tugas</th>
                                            <th>Deadline</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $conn->prepare("SELECT * FROM admin_tasks");
                                        $stmt->execute();
                                        $tasks = $stmt->get_result();
                                        ?>
                                        <?php $i = 1; ?>
                                        <?php foreach ($tasks as $data) : ?>
                                            <tr>
                                                <td><?= $i; ?></td>
                                                <td><?= htmlspecialchars($data['judul_tugas']); ?></td>
                                                <td><?= htmlspecialchars($data['deskripsi_tugas']); ?></td>
                                                <td><?= htmlspecialchars($data['deadline']); ?></td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal<?= $data['id'] ?>">Edit</a>
                                                    <br><br>
                                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#hapusModal<?= $data['id'] ?>">Hapus</a>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="editModal<?= $data['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel">Edit Tugas</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="" method="POST">
                                                                <input type="hidden" name="id_tugas" value="<?= $data['id']; ?>">
                                                                <div class="form-group">
                                                                    <label for="judul_tugas">Judul Tugas:</label>
                                                                    <input type="text" class="form-control" id="judul_tugas" name="judul_tugas" value="<?= htmlspecialchars($data['judul_tugas']); ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="deskripsi_tugas">Deskripsi Tugas:</label>
                                                                    <textarea class="form-control" id="deskripsi_tugas" name="deskripsi_tugas" required><?= htmlspecialchars($data['deskripsi_tugas']); ?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="deadline">Deadline:</label>
                                                                    <input type="datetime-local" class="form-control" id="deadline" name="deadline" value="<?= htmlspecialchars($data['deadline']); ?>" required>
                                                                </div>
                                                                <button type="submit" name="edit" class="btn btn-primary w-100">Simpan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="hapusModal<?= $data['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Hapus Tugas</h5>
                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus tugas berjudul: <b><?= htmlspecialchars($data['judul_tugas']) ?></b>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                                            <form action="" method="post">
                                                                <input type="hidden" name="id_tugas" value="<?= $data['id'] ?>">
                                                                <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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