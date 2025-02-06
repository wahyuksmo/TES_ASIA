<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes ASIA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">CRUD Client dengan Redis & S3</h2>
        
        <form id="clientForm">
            <input type="hidden" id="client_id">

            <div class="mb-3">
                <label>Nama Client</label>
                <input type="text" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Slug</label>
                <input type="text" id="slug" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Client Prefix</label>
                <input type="text" id="client_prefix" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Address</label>
                <input type="text" id="address" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>City</label>
                <input type="text" id="city" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Phone number</label>
                <input type="text" id="phone_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Logo Client</label>
                <input type="file" id="client_logo" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>

        <hr>

        <!-- Tabel Data -->
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Prefix</th>
                    <th>Logo</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Phone Number</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="clientTableBody">
            </tbody>
        </table>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            loadClients();


            $("#clientForm").on("submit", function (e) {
                e.preventDefault();
                
                let formData = new FormData();
                formData.append("name", $("#name").val());
                formData.append("slug", $("#slug").val());
                formData.append("client_prefix", $("#client_prefix").val());
                formData.append("address", $("#address").val());
                formData.append("city", $("#city").val());
                formData.append("phone_number", $("#phone_number").val());


                

                let file = $("#client_logo")[0].files[0];
                if (file) {
                    formData.append("client_logo", file);
                }

                let clientId = $("#client_id").val();
                let method = clientId ? "PUT" : "POST";
                let url = clientId ? `/api/clients/${clientId}` : "/api/clients";

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert("Data berhasil disimpan!");
                        $("#clientForm")[0].reset();
                        $("#client_id").val("");
                        loadClients();
                    },
                    error: function () {
                        alert("Terjadi kesalahan!");
                    }
                });
            });

            function loadClients() {
                $.ajax({
                    url: "/api/clients",
                    type: "GET",
                    success: function (response) {
                        let rows = "";
                        response.forEach(client => {
                            rows += `
                                <tr>
                                    <td>${client.name}</td>
                                    <td>${client.slug}</td>
                                    <td>${client.client_prefix}</td>
                                    <td>${client.adress}</td>
                                    <td>${client.city}</td>
                                    <td>${client.phone_number}</td>
                                    <td><img src="${client.client_logo}" width="50"></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit" data-id="${client.id}">Edit</button>
                                        <button class="btn btn-danger btn-sm delete" data-id="${client.id}">Hapus</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $("#clientTableBody").html(rows);
                    }
                });
            }

            $(document).on("click", ".edit", function () {
                let id = $(this).data("id");

                $.ajax({
                    url: `/api/clients/${id}`,
                    type: "GET",
                    success: function (client) {
                        $("#client_id").val(client.id);
                        $("#name").val(client.name);
                        $("#slug").val(client.slug);
                        $("#client_prefix").val(client.client_prefix);
                        $("#address").val(client.address)
                        $("#city").val(client.city)
                        $("#phone_number").val(client.phone_number)
                        
                    }
                });
            });

            $(document).on("click", ".delete", function () {
                if (confirm("Yakin ingin menghapus data ini?")) {
                    let id = $(this).data("id");

                    $.ajax({
                        url: `/api/clients/${id}`,
                        type: "DELETE",
                        success: function () {
                            alert("Data berhasil dihapus!");
                            loadClients();
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>
