<?php
include 'koneksi.php';

// Handle Add Schedule Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $nama_pasien = strtolower($_POST['pasien']); // Convert to lowercase
    $id_konsultan = $_POST['id_konsultan'];
    $tanggal = $_POST['tanggal'];
    $sesi = $_POST['sesi'];

    // Find the ID of the patient from the database
    $query_pasien = "SELECT id FROM pasien WHERE nama='$nama_pasien'";
    $result_pasien = mysqli_query($conn, $query_pasien);
    $data_pasien = mysqli_fetch_assoc($result_pasien);

    if ($data_pasien) {
        $id_pasien = $data_pasien['id'];

        // Insert the schedule data into the database
        $insert_query = "INSERT INTO jadwal (id_pasien, id_konsultan, tanggal, sesi) VALUES ('$id_pasien', '$id_konsultan', '$tanggal', '$sesi')";
        if (mysqli_query($conn, $insert_query)) {
            echo "Reservasi berhasil ditambahkan!";
            header("Location: jadwal.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Pasien dengan nama '$nama_pasien' tidak ditemukan.";
    }
}

// Handle Delete Schedule Request
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Delete the schedule data from the database
    $delete_query = "DELETE FROM jadwal WHERE id='$id'";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: index.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Fetch the schedule data from the database
$query = "SELECT * FROM jadwal";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <title>Daftar</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                Sistem Appointment Udinus Career Center
            </a>
            <button class="navbar-toggler"
                type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false"
                aria-label="Toggle navigation">
            </button>
            <div class="d-flex" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="jadwal.php">
                            Jadwal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="konsultan.php">
                            Konsultan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="pasien.php">
                            Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Pendaftaran Appointment</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="pasien">Nama Pasien</label>
                            <input type="text" name="pasien" class="form-control" id="pasien" required>
                        </div>
                        <div class="form-group">
                            <label for="konsultan">Nama Konsultan</label>
                            <select name="id_konsultan" class="form-control">
                                <?php
                                $konsultan = mysqli_query($conn, "SELECT * FROM konsultan");
                                while ($k = mysqli_fetch_assoc($konsultan)) {
                                    echo "<option value='{$k['id']}'>{$k['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="sesi">Sesi</label>
                            <select name="sesi" class="form-control" id="sesi" required>
                                <option value="8.00 - 9.00">8.00 - 9.00</option>
                                <option value="9.00 - 10.00">9.00 - 10.00</option>
                                <option value="10.00 - 11.00">10.00 - 11.00</option>
                                <option value="13.00 - 14.00">13.00 - 14.00</option>
                                <option value="14.00 - 15.00">14.00 - 15.00</option>
                            </select>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary mt-2">Daftar</button>
                    </form>
                </div>
            </div>
            <div class="container mt-5">
                <h2 class="mt-5">Data Jadwal Appointment</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pasien</th>
                            <th>Nama Konsultan</th>
                            <th>Tanggal</th>
                            <th>Sesi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $jadwal = mysqli_query($conn, "SELECT jadwal.*, pasien.nama as nama_pasien, konsultan.nama as nama_konsultan FROM jadwal
                        JOIN pasien ON jadwal.id_pasien = pasien.id
                        JOIN konsultan ON jadwal.id_konsultan = konsultan.id");
                        $no = 1;
                        while ($j = mysqli_fetch_assoc($jadwal)) : ?>
                            <tr>
                                <td> <?= $no ?></td>
                                <td><?= ucwords($j['nama_pasien']) ?></td>
                                <td><?= $j['nama_konsultan'] ?></td>
                                <td><?= $j['tanggal'] ?></td>
                                <td><?= $j['sesi'] ?></td>
                                <td>
                                    <a href="edit_jadwal.php?edit_id= <?= $j['id'] ?>" class="btn btn-warning">Edit</a>
                                    <a href="?delete_id=<?= $j['id'] ?>" class='btn btn-danger'>Delete</a>
                                </td>
                            </tr>
                        <?php $no++;
                        endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--  -->
    

</body>

</html>