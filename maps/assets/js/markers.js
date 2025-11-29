fetch('assets/php/features.php')
    .then(r => r.json())
    .then(data => {
        const targets = data.features;

        targets.forEach(target => {
            const name = target.properties.name;
            const [lng, lat] = target.geometry.coordinates;

            // create marker
            const marker = L.marker([lat, lng]).addTo(map);
            
            // simple popup
            marker.bindPopup(`Checkpoint ${name}`);

            // optional: handle click
            marker.on('click', () => {
                console.log("Clicked checkpoint:", name);
                // show CP Option Card etc.
                const cpCard = document.getElementById(`cp_option_card_${name}`);
                cpCard.classList.add("active");
            });

            marker.on('click', () => {
                const cpCard = document.getElementById(`cp_option_card_${name}`);
                cpCard.classList.add("active");
            });
        });
    });

    
