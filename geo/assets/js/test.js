console.log("tested");
$("body").on("click", "button", function () {

    console.log("button clicked");
    var cp = $(this).attr('cp');
    console.log (cp);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'assets/php/test.php?cp='+cp,
        success: function(data) {
            console.log("ajax return");
            alert(data["available_cps"]);           
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
})