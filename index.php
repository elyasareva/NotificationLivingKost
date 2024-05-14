<?php
// Koneksi ke database
$host = "localhost";
$username = "username";
$password = "password";
$database = "nama_database";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Fungsi untuk mendapatkan data notifikasi dalam bentuk JSON
function getNotifications() {
    global $conn;
    $sql = "SELECT * FROM notifikasi";
    $result = $conn->query($sql);

    $notifications = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
    return json_encode($notifications);
}

// Proses penghapusan data notifikasi
if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM notifikasi WHERE id = $delete_id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}

// Ambil data notifikasi dalam bentuk JSON
$notifications = getNotifications();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Notifikasi</title>
</head>
<body>
    <h1>Halaman Notifikasi</h1>
    <div id="notifications">
        <!-- Tampilan data notifikasi akan ditampilkan di sini -->
    </div>

    <script>
        // Fungsi untuk menampilkan notifikasi dari JSON
        function showNotifications() {
            var notifications = <?php echo $notifications; ?>;
            var html = '';
            notifications.forEach(function(notification) {
                html += '<div>';
                html += '<p>Nama Pencari Kost: ' + notification.nama + '</p>';
                html += '<p>Bukti Pembayaran: ' + notification.bukti_pembayaran + '</p>';
                html += '<p>Status Sewa: ' + notification.status_sewa + '</p>';
                html += '<button onclick="deleteNotification(' + notification.id + ')">Hapus</button>';
                html += '</div>';
            });
            document.getElementById('notifications').innerHTML = html;
        }

        // Fungsi untuk menghapus notifikasi menggunakan AJAX
        function deleteNotification(id) {
            if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == XMLHttpRequest.DONE) {
                        if (xhr.status == 200) {
                            alert(xhr.responseText);
                            showNotifications();
                        } else {
                            alert('Terjadi kesalahan: ' + xhr.status);
                        }
                    }
                };
                xhr.open('GET', 'halaman_notifikasi.php?delete_id=' + id, true);
                xhr.send();
            }
        }

        // Panggil fungsi untuk menampilkan notifikasi saat halaman dimuat
        showNotifications();
    </script>
</body>
</html>
