// assets/js/ajax-search.js

$(document).ready(function () {
    $('#search-input').on('input', function () {
        var searchTerm = $(this).val();

        $.ajax({
            url: '/ajax-search',
            type: 'GET',
            data: { q: searchTerm },
            success: function (data) {
                // Handle the data and update your UI dynamically
                // For example, replace the content of the table body
                $('#search-results').html(data);
            }
        });
    });
});
