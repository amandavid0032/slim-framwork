$(document).ready(function () {
    // Function to fetch data and render the table
    function fetchData() {
        $.ajax({
            url: 'http://localhost:8888/api/data',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                renderData(data);
            },
            error: function (xhr, status, error) {
                // Handle errors
                console.error(xhr.responseText);
            }
        });
    }

    // Initial fetch of data
    fetchData();

    function renderData(data) {
        $('#data-container').empty();
        data.forEach(function (item) {
            var row = $('<tr>');
            row.append($('<td>').text(item.id));
            row.append($('<td>').text(item.f_name));
            row.append($('<td>').text(item.l_name));
            row.append($('<td>').text(item.emailId));
            row.append($('<td>').text(item.gender));
            row.append($('<td>').html('<button class="btn btn-primary btn-sm edit-btn">Edit</button>&nbsp;<button class="btn btn-danger btn-sm delete-btn">Delete</button>'));
            $('#data-container').append(row);
        });

        // Add event listeners for edit and delete buttons
        $('.edit-btn').click(function () {
            var rowIndex = $(this).closest('tr').index();
            var rowData = data[rowIndex];
        
            $.ajax({
                url: 'http://localhost:8888/api/single/' + rowData.id,
                type: 'POST',
                success: function (response) {
                    var modalContent = `
                        <div class="modal-header" id="userDataForm">
                            <h5 class="modal-title">Edit User Data</h5>
                        </div>
                        <div class="modal-body">
                            <form id="editForm">
                                <input type="hidden" name="id" value="${response.id}">
                                <p>First Name: <input name="f_name" value="${response.f_name}"></p>
                                <p>Last Name: <input name="l_name" value="${response.l_name}"></p>
                                <p>Email Id: <input name="emailId" value="${response.emailId}"></p>
                                <p>Age: <input name="age" value="${response.age}"></p>
                                <p>Phone Number: <input name="phone" value="${response.phone}"></p>
                                <p>Gender: <input name="gender" value="${response.gender}"></p>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="submit" class="btn btn-secondary" data-dismiss="modal">Submit</button>
                        </div>
                    `;
                    $('.modal-content').html(modalContent);
                    $('#userData').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error("Failed to fetch user data:", xhr.responseText);
                }
            });
        });

        // Handle form submission
        $('#userData').on('click', '#submit', function () {
            var formData = $('#editForm').serialize();
            var id = $('#editForm input[name="id"]').val();
            console.log(id);
            $.ajax({
                url: 'http://localhost:8888/api/Update/'+id,
                type: 'PUT',
                data: formData,
                success: function (response) {
                    alert("Record updated successfully");
                    $('#userData').modal('hide');
                    fetchData();
                },
                error: function (xhr, status, error) {
                    console.error("Failed to update record:", xhr.responseText);
                }
            });
        });

        $('.delete-btn').click(function () {
            var rowIndex = $(this).closest('tr').index();
            var rowData = data[rowIndex];
            $.ajax({
                url: 'http://localhost:8888/api/delete/' + rowData.id,
                type: 'DELETE',
                success: function (result) {
                    alert("Data deleted successfully");
                    data.splice(rowIndex, 1);
                    renderData(data);
                },
                error: function (xhr, status, error) {
                    console.error("Delete failed:", xhr.responseText);
                }
            });
        });
    }

    
});
