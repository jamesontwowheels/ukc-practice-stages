$(document).ready(function() {
    ajax_call;
})
console.log("tested");
$("body").on("click", "button", ajax_call);

function ajax_call() {
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
            //running score
            document.getElementById("score_zone").innerHTML = data["running_score"];
            
            //commentary
            const items = data["commentary"];
            items.reverse();
            const itemList = document.getElementById('commentary-list');
            itemList.empty();

            // Loop through the array and append each item to the DOM
            items.forEach(item => {
                // Create a new DOM element (e.g., <li> for list items)
                const listItem = document.createElement('li');
    
                // Set the text content of the element to the array item
                listItem.textContent = item;
    
                // Append the new element to the target DOM element
                itemList.appendChild(listItem);
            });
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
}