<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Access Logs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="mb-3">
            <p><strong>Your IP Address:</strong> <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
        </div>

        <h2>RFID Access Logs</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>RFID ID</th>
                        <th>Name</th>
                        <th>Entry Time</th>
                        <th>Exit Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="logTable"></tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.2/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            function loadLogs() {
                $.ajax({
                    url: 'api/rfid_api.php',
                    method: 'GET',
                    success: function(data) {
                        let tableContent = '';
                        data.forEach(log => {
                            tableContent += `<tr>
                                <td>${log.id}</td>
                                <td>${log.rfid_id}</td>
                                <td>${log.name}</td>
                                <td>${log.entry_time || '-'}</td>
                                <td>${log.exit_time || '-'}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm deleteBtn" data-id="${log.id}">Delete</button>
                                </td>
                            </tr>`;
                        });
                        $('#logTable').html(tableContent);
                    }
                });
            }

            loadLogs();
            setInterval(loadLogs, 5000);

            $(document).on('click', '.deleteBtn', function() {
                const logId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This log will be deleted permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/rfid_api.php/delete',
                            method: 'DELETE',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                id: logId
                            }),
                            success: function(response) {
                                if (response.message) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    loadLogs();
                                } else {
                                    Swal.fire('Error!', 'Failed to delete log.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'An error occurred while deleting log.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>