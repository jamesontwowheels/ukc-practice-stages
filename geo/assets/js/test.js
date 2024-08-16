console.log("tested");
$("body").on("click", "button", function () {

    console.log("button clicked / name update");
    var cp = $(this).attr('cp');
    console.log (cp);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/geo/assets/php/test.php?cp='+cp,
        success: function(data) {
            console.log("ajax return");
            document.getElementById("inventory_zone").innerHTML = "Current word =" + data["current_word"] + "<br> Current bonus = " + data["current_bonus"];
            var cp_names = data["cp_names"];
            var cp_names_keys = Object.keys(cp_names);
            let i = 0;
            while (i < cp_names_keys.length) {
            var this_key = cp_names_keys[i];
            var cp_id = "butt" + this_key;
            document.getElementById(cp_id).innerHTML = cp_names[this_key];
            i++;
            } 
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
})