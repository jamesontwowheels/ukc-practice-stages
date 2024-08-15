console.log("tested");
$("body").on("click", "button", function () {

    console.log("button clicked");
    var cp = $(this).attr('cp');
    console.log (cp);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/geo/assets/php/test.php?cp='+cp,
        success: function(data) {
            console.log("ajax return");
            alert(data["debug_log"]);
            //document.getElementById(inventory_zone).innerHTML = data["current_word"];         
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
})