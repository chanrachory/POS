$(document).ready(function () {
    $("#stockForm").on("submit", function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "insert.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                alert(response);
                $("#stockForm")[0].reset(); // Clear the form after success
            }
        });
    });
});
