// Function to detect platform and set instructions
function setInstallInstructions() {
    const instructionText = document.getElementById("instruction-text");

    // Detect if on iOS or Android
    const userAgent = navigator.userAgent || navigator.vendor || window.opera;
    if (/android/i.test(userAgent)) {
        // Android instructions
        instructionText.innerHTML = `
            To install the app on your Android device:<br><br>
            1. Open this page in Chrome.<br>
            2. Tap the menu icon (three dots) in the upper-right corner.<br>
            3. Select <strong>"Add to Home Screen"</strong>.<br>
            4. Confirm by tapping <strong>"Add"</strong>.<br>
            Enjoy the app!
        `;
    } else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        // iOS instructions
        instructionText.innerHTML = `
            To install the app on your iOS device:<br><br>
            1. Open this page in Safari.<br>
            2. Tap the <strong>Share</strong> icon (the square with an arrow pointing up) at the bottom of the screen.<br>
            3. Select <strong>"Add to Home Screen"</strong> from the options.<br>
            4. Tap <strong>"Add"</strong> to confirm.<br>
            Now you’re ready to go!
        `;
    } else {
        // Default message for other devices
        instructionText.innerHTML = `
            To install our app, open this page in a compatible browser like Chrome on Android or Safari on iOS, and follow the prompts to add it to your home screen.
        `;
    }
}

// Run the function on page load
document.addEventListener("DOMContentLoaded", setInstallInstructions);
