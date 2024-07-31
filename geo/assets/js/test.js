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
            alert(data);
            $("p").text(data);
            

        }
    });
})