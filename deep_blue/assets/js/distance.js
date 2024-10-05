let id;
let target;
let options;

if (user_ID == 29) { 
  var d_need = 150000;
  console.log("GOD MODE ACTIVE");} 
  else { var d_need = 15;} // god mode

fetch('assets/php/location.php')
        .then(response => response.json())
        .then(data => {
            var location = data.location;
            console.log (location);
            var targets = games[location];
            console.log (targets);

function deg2rad(degrees) {
  return degrees * (Math.PI / 180);
}

function success(pos) {

  for (let i = 0; i < targets.length; i++) {
  var target = targets[i];
  var id = target.properties.name;

  const crd = pos.coords;
  // document.getElementById(id).innerHTML = crd.latitude;
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(crd.latitude-target.geometry.coordinates[1]);  // deg2rad below
  var dLon = deg2rad(crd.longitude-target.geometry.coordinates[0]); 
  
  var a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(deg2rad(crd.latitude)) * Math.cos(deg2rad(target.geometry.coordinates[1])) * 
    Math.sin(dLon/2) * Math.sin(dLon/2)
    ; 
    
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
  var d = Math.round(R * c * 1000); // Distance in m
  cp = "cp" + id;
  document.getElementById(cp).innerHTML = "CP"+id + " - "+ d + "m away";
  var button = "butt"+id;
  var button_element = $("#"+button);

  if (d < d_need) {
    // redundant: document.getElementById(cp).innerHTML = document.getElementById(cp).innerHTML + "check-in"
    button_element.addClass('active');
    button_element.removeClass('inactive')

  } else {
    button_element.addClass('inactive');
    button_element.removeClass('active')
    document.getElementById(cp).innerHTML = "CP"+id + " - "+ d + "m away";
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
        