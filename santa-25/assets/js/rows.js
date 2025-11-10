//fetch('assets/php/location.php')
fetch('assets/php/features.php')
        .then(response => response.json())
        .then(data => {
            //var location = data.location;
            //var targets = games[location];
            console.log (data);
            var targets = data.features;
            window.targets = targets;
            console.log (targets);
const tableBody = document.querySelector('#checkpoints tbody');
const cp_options = document.querySelector('#cp_options');
for (let i = 0; i < targets.length; i++) {
                target = targets[i];
                const row = document.createElement('tr');
                row.id = "row"+target.properties.name;
                row.innerHTML = `
                    <td id="button${target.properties.name}"><button id="butt${target.properties.name}" class="inactive cp_button submit_button" cp="${target.properties.name}" class='check_in'>${target.properties.name}</button></td>
                    <td id="cp${target.properties.name}" class="right_column">${target.properties.name}</td>
                `;
                tableBody.appendChild(row);

                
                const newDiv = document.createElement('div');
                newDiv.id = `cp_option_card_${target.properties.name}`;
                newDiv.innerHTML = `<div id="cp-header-${target.properties.name}" class="cp-header">${target.properties.name}</div><span class="close-btn">&times;</span><div id="cp_comment_space_${target.properties.name}" class="cp_comment"></div><div id="cp_info_space_${target.properties.name}"></div><div id="cp_option_space_${target.properties.name}"></div>`;
                newDiv.classList.add('cp-option');
                cp_options.appendChild(newDiv);
            }

        })
