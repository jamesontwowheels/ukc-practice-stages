const tableBody = document.querySelector('#checkpoints tbody');
for (let i = 0; i < targets.length; i++) {
                console.log(targets[i].properties.name);
                target = targets[i];
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td id="cp${target.properties.name}">${target.properties.name}</td>
                    <td id="button${target.properties.name}"></td>
                `;
                console.log(row);
                tableBody.appendChild(row);
            }