const tableBody = document.querySelector('#checkpoints tbody');
for (let i = 0; i < targets.length; i++) {
                console.log(targets[i].properties.name);
                target = targets[i];
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td id="button${target.properties.name}"><button id="butt${target.properties.name}" cp="${target.properties.name}" class='check_in'>${target.properties.name}</button></td>
                    <td id="cp${target.properties.name}">${target.properties.name}</td>
                `;
                console.log(row);
                tableBody.appendChild(row);
            }