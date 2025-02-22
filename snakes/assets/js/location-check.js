document.addEventListener("DOMContentLoaded", () => {
    const statusText = document.getElementById("status");
    const accuracyText = document.getElementById("accuracy");
    const requestBtn = document.getElementById("requestBtn");

    function updateLocation() {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                statusText.textContent = "Permission Status: granted";
                requestBtn.style.display = "none";
                accuracyText.textContent = 
                    "Accuracy: ±" + position.coords.accuracy.toFixed(2) + " meters";
            },
            (error) => {
                statusText.textContent = "Permission Status: denied";
                accuracyText.textContent = "Error: " + error.message;
            },
            { enableHighAccuracy: true }
        );
    }

    function checkPermission() {
        if (!navigator.geolocation) {
            statusText.textContent = "Geolocation is not supported by this browser.";
            return;
        }

        navigator.permissions.query({ name: "geolocation" }).then(permissionStatus => {
            statusText.textContent = "Permission Status: " + permissionStatus.state;
            
            if (permissionStatus.state === "granted") {
                updateLocation();
            } else if (permissionStatus.state === "prompt") {
                requestBtn.style.display = "inline-block";
            }

            permissionStatus.onchange = () => {
                statusText.textContent = "Permission Status: " + permissionStatus.state;
                if (permissionStatus.state === "granted") {
                    updateLocation();
                }
            };
        }).catch(() => {
            statusText.textContent = "Permission status check failed.";
        });
    }

    requestBtn.addEventListener("click", updateLocation);
    checkPermission();
});
