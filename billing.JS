$(document).ready(function () {
    // Load Categories
    $.ajax({
        url: "fetch_categories.php",
        type: "GET",
        success: function (response) {
            $("#categoriesList").html(response);
        }
    });

    // Load Items
    function loadItems(category = "") {
        $.ajax({
            url: "fetch_items.php",
            type: "GET",
            data: { category: category },
            success: function (response) {
                $("#itemsGrid").html(response);
            }
        });
    }

    // Click event for categories
    $(document).on("click", ".category-item", function () {
        let category = $(this).text();
        loadItems(category);
    });
});
