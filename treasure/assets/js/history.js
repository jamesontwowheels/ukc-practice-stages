

$(document).ready(ajax_call);

function ajax_call() {
    cp = 0;
    
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'assets/php/test.php?purpose=3&cp='+cp,
        success: function(data) {
            console.log("ajax return");
            var debug_log = data["debug_log"];
            console.log(debug_log);

            // running score
            // document.getElementById("score_zone").innerHTML = data["running_score"];
            
            //commentary
            const items = data["commentary"];
            console.log (items);
            items.reverse();
            const itemList = document.getElementById('commentary-list');
            itemList.innerHTML = '';

                // Loop through the array and append each item to the DOM
                items.forEach(item => {
                    // Create a new DOM element (e.g., <li> for list items)
                    const listItem = document.createElement('li');
        
                    // Set the text content of the element to the array item
                    listItem.innerHTML = item;
                    listItem.addClass = "history_list";
        
                    // Append the new element to the target DOM element
                    itemList.appendChild(listItem);
            });


            
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
        
    });
}



