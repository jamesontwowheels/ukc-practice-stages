fetch('/geo/assets/php/location.php')
        .then(response => response.json())
        .then(data => {
            var location = data.location;
            console.log (location);
            var targets = games[location];
            console.log (targets);
const tableBody = document.querySelector('#checkpoints tbody');
for (let i = 0; i < targets.length; i++) {
                console.log(targets[i].properties.name);
                target = targets[i];
                const row = document.createElement('tr');
                row.id = "row"+target.properties.name;
                row.innerHTML = `
                    <td id="button${target.properties.name}"><button id="butt${target.properties.name}" class="inactive" cp="${target.properties.name}" class='check_in'>${target.properties.name}</button></td>
                    <td id="cp${target.properties.name}">${target.properties.name}</td>
                `;
                console.log(row);
                tableBody.appendChild(row);
            }

        ajax_call();
        })
