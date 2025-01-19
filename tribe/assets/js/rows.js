fetch('assets/php/location.php')
        .then(response => response.json())
        .then(data => {
            var location = data.location;
            console.log (location);
            var targets = games[location];
            console.log (targets);
const tableBody = document.querySelector('#checkpoints tbody');
const cp_options = document.querySelector('#cp_options');
for (let i = 0; i < targets.length; i++) {
                console.log(targets[i].properties.name);
                target = targets[i];
                const row = document.createElement('tr');
                row.id = "row"+target.properties.name;
                row.innerHTML = `
                    <td id="button${target.properties.name}"><button id="butt${target.properties.name}" class="inactive cp_button" cp="${target.properties.name}" class='check_in'>${target.properties.name}</button></td>
                    <td id="cp${target.properties.name}">${target.properties.name}</td>
                `;
                console.log(row);
                tableBody.appendChild(row);

                
                const newDiv = document.createElement('div');
                newDiv.id = `cp_option_card_${target.properties.name}`;
                newDiv.innerHTML = `<div class="cp-header">${target.properties.name}</div><span class="close-btn">&times;</span><div id="cp_info_space_${target.properties.name}"></div><div id="cp_option_space_${target.properties.name}"></div>`;
                newDiv.classList.add('cp-option');
                cp_options.appendChild(newDiv);
            }
        })
