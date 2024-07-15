console.log("tested");
$("body").on("click", "button", function () {

    console.log("button clicked");
    var cp = $(this).attr('cp');
    console.log (cp);
    $.ajax({
        type: 'POST',
        url: 'assets/php/script.php?cp='+cp,
        success: function(data) {
            alert(data);
            $("p").text(data);

        }
    });
})