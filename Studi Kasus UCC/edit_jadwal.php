<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <title>Edit Reservasi</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Sistem Appointment Udinus Career Center</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation"></button>
            <div class="d-flex" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="jadwal.php">Jadwal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pasien.php">Pasien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="konsultan.php">Konsultan</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php
    include 'koneksi.php';

    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];

        // Query untuk mengambil data jadwal dengan join ke tabel pasien
        $edit_query = "
            SELECT jadwal.*, pasien.nama AS nama_pasien 
            FROM jadwal 
            JOIN pasien ON jadwal.id_pasien = pasien.id 
            WHERE jadwal.id='$edit_id'
        ";
        $edit_result = mysqli_query($conn, $edit_query);
        $edit_data = mysqli_fetch_assoc($edit_result);

        // Handle Edit Schedule Request
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
            $id = $_POST['id'];
            $nama_pasien = $_POST['pasien'];
            $id_konsultan = $_POST['id_konsultan'];
            $tanggal = $_POST['tanggal'];
            $sesi = $_POST['sesi'];

            // Format nama pasien menjadi lowercase
            $nama_pasien = strtolower($nama_pasien);

            // Ambil ID pasien berdasarkan nama pasien
            $pasien_query = "SELECT id FROM pasien WHERE LOWER(nama)='$nama_pasien'";
            $pasien_result = mysqli_query($conn, $pasien_query);
            $pasien_data = mysqli_fetch_assoc($pasien_result);
            $id_pasien = $pasien_data['id'];

            // Update the schedule data in the database
            $update_query = "UPDATE jadwal SET id_pasien='$id_pasien', id_konsultan='$id_konsultan', tanggal='$tanggal', sesi='$sesi' WHERE id='$id'";
            if (mysqli_query($conn, $update_query)) {
                header("Location: jadwal.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    ?>
        <div class="container mt-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Data Jadwal Appointment</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <div class="form-group">
                            <label for="pasien">Nama Pasien</label>
                            <input type="text" name="pasien" class="form-control" id="pasien" value="<?php echo ucwords($edit_data['nama_pasien']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="konsultan">Nama Konsultan</label>
                            <select name="id_konsultan" class="form-control" id="konsultan">
                                <?php
                                $konsultan = mysqli_query($conn, "SELECT * FROM konsultan");
                                while ($k = mysqli_fetch_assoc($konsultan)) {
                                    $selected = $edit_data['id_konsultan'] == $k['id'] ? 'selected' : '';
                                    echo "<option value='{$k['id']}' $selected>{$k['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" id="tanggal" value="<?php echo $edit_data['tanggal']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="sesi">Sesi</label>
                            <select name="sesi" class="form-control" id="sesi" required>
                                <option value="8.00 - 9.00" <?php if ($edit_data['sesi'] == '8.00 - 9.00') echo 'selected'; ?>>8.00 - 9.00</option>
                                <option value="9.00 - 10.00" <?php if ($edit_data['sesi'] == '9.00 - 10.00') echo 'selected'; ?>>9.00 - 10.00</option>
                                <option value="10.00 - 11.00" <?php if ($edit_data['sesi'] == '10.00 - 11.00') echo 'selected'; ?>>10.00 - 11.00</option>
                                <option value="13.00 - 14.00" <?php if ($edit_data['sesi'] == '13.00 - 14.00') echo 'selected'; ?>>13.00 - 14.00</option>
                                <option value="14.00 - 15.00" <?php if ($edit_data['sesi'] == '14.00 - 15.00') echo 'selected'; ?>>14.00 - 15.00</option>
                            </select>
                        </div>
                        <button type="submit" name="edit" class="btn btn-success mt-2">Update</button>
                    </form>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</body>

</html>