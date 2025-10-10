let id;
let target;
let options;
let button_detail;

if (user_ID == 29) { 
  var d_base = 30000000;
  console.log("GOD MODE ACTIVE");} 
  else if (user_ID == 8) {
  var d_base = 250000000;
  
  console.log("demi-god mode active");    
  } else { var d_base = 25;} // god mode

fetch('assets/php/location.php')
        .then(response => response.json())
        .then(data => {
            var location = data.location;
            console.log (location);
            console.log (window.targets)
           // var targets = games[location];
            console.log (targets);

function deg2rad(degrees) {
  return degrees * (Math.PI / 180);
}

//adding in the bearing functions here:
      function calculateBearing(lat1, lon1, lat2, lon2) {
        // Convert degrees to radians
        const toRadians = degrees => degrees * (Math.PI / 180);
        const toDegrees = radians => radians * (180 / Math.PI);

        const lat1Rad = toRadians(lat1);
        const lat2Rad = toRadians(lat2);
        const deltaLonRad = toRadians(lon2 - lon1);

        // Calculate bearing using the formula
        const y = Math.sin(deltaLonRad) * Math.cos(lat2Rad);
        const x = Math.cos(lat1Rad) * Math.sin(lat2Rad) -
                  Math.sin(lat1Rad) * Math.cos(lat2Rad) * Math.cos(deltaLonRad);
        let bearing = toDegrees(Math.atan2(y, x));

        // Normalize to 0–360 degrees
        bearing = (bearing + 360) % 360;
        return bearing;
      }

      function getCompassDirection(bearing) {
        const directions = [
            "North", "North-East", "East", "South-East",
            "South", "South-West", "West", "North-West", "North"
        ];
        const index = Math.round(bearing / 45);
        return directions[index % 8];
      }

function success(pos) {

  for (let i = 0; i < targets.length; i++) {
  var target = targets[i];
  var id = target.properties.name;
console.log(target);
  const crd = pos.coords;
  var accuracy = Math.min(20, Math.round(crd.accuracy));
  
  d_need = d_base + accuracy; //generous range
  // document.getElementById("accuracy_zone").innerHTML = accuracy + "m radius:" + d_need;
    // document.getElementById(id).innerHTML = crd.latitude;
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(crd.latitude-target.geometry.coordinates[1]);  // deg2rad below
  var dLon = deg2rad(crd.longitude-target.geometry.coordinates[0]); 
  
  var a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(deg2rad(crd.latitude)) * Math.cos(deg2rad(target.geometry.coordinates[1])) * 
    Math.sin(dLon/2) * Math.sin(dLon/2)
    ; 
    const bearing = calculateBearing(crd.latitude, crd.longitude, target.geometry.coordinates[1], target.geometry.coordinates[0]);
    const direction = getCompassDirection(bearing);
    

  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
  var d = Math.round(R * c * 1000); // Distance in m
  var cp = "cp" + id;
  var option_element = $("#cp_option_card_" + id);
  console.log("cp = "+cp);
  document.getElementById(cp).innerHTML = d + "m " + direction;
  console.log("cp = "+cp);
  var button = "butt"+id;
  var button_element = $("#"+button);



  if (d < d_need) {
    button_element.addClass('active');
    button_element.removeClass('inactive');
    if (option_element.hasClass('available') && option_element.hasClass('show_first') && user_ID !== 29) {
      option_element.removeClass('cp-option');
      option_element.removeClass('show_first');
      console.log('removing show first');
      option_element.addClass('cp-option-show');}
  } else {
    button_element.addClass('inactive');
    button_element.addClass('show_first');
    button_element.removeClass('active');
    //this is where we need to add the 'hide the options'
    option_element.addClass('cp-option');
    option_element.removeClass('cp-option-show');
  }
}
}


function error(err) {
  alert("location check failed. Code: " + err.code + " Message:" + err.message );
  console.error(`ERROR(${err.code}): ${err.message}`);
}

options = {
  enableHighAccuracy: true,
  timeout: 10000,
  maximumAge: 0,
};

            id = navigator.geolocation.watchPosition(success, error, options);
        })
        