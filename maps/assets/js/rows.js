fetch('assets/php/features.php')
    .then(response => response.json())
    .then(data => {

        const targets = data.features;
        window.targets = targets;

        const cp_options = document.querySelector('#cp_options');

        for (let i = 0; i < targets.length; i++) {
            const target = targets[i];
            const id = target.properties.name;

            //
            // 1) Create cp-option card (unchanged)
            //
            const optionCard = document.createElement('div');
            optionCard.id = `cp_option_card_${id}`;
            optionCard.innerHTML = `
                <div id="cp-header-${id}" class="cp-header">${id}</div>
                <span class="close-btn">&times;</span>
                <div id="cp_comment_space_${id}" class="cp_comment"></div>
                <div id="cp_info_space_${id}"></div>
                <div id="cp_option_space_${id}"></div>
            `;
            optionCard.classList.add('cp-option');
            cp_options.appendChild(optionCard);

            //
            // 2) Create popup content using your original two cells
            //
            const popupDiv = document.createElement('div');
            popupDiv.innerHTML = `
                <table>
                    <tr id="row${id}">
                        <td id="button${id}">
                            <button id="butt${id}" class="inactive cp_button submit_button" cp="${id}">
                                ${id}
                            </button>
                        </td></tr><tr>
                        <td id="cp${id}" class="right_column">${id}</td>
                    </tr>
                </table>
            `;

            //
            // 3) Create marker at coords
            //
            const [lng, lat] = target.geometry.coordinates;
            const marker = L.marker([lat, lng]).addTo(map);

            marker.bindPopup(popupDiv);

            //
            // 4) Optionally: store references
            //
            window[`cp_${id}`] = {
                optionCard,
                marker,
                popupDiv
            };
        }

            window.dispatchEvent(new Event("targetsReady"));
    });
