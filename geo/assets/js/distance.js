let id;
let target;
let options;

function deg2rad(degrees) {
  return degrees * (Math.PI / 180);
}


function success(pos) {

  for (let i = 0; i < targets.length; i++) {
  var target = targets[i];
 console.log(target);
  var id = target.properties.name;

  const crd = pos.coords;
console.log(crd);
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
  document.getElementById(cp).innerHTML = d + "m away";
  var button = "butt"+id;
  var button_element = $("#"+button);
  if (d < 30000) {
    document.getElementById(cp).innerHTML = document.getElementById(cp).innerHTML + " Check-in"
    button_element.addClass('active');
    button_element.removeClass('inactive')

  } else {
    button_element.addClass('inactive');
    button_element.removeClass('active')
    document.getElementById(cp).innerHTML = d + "m away";
  }
}
}


function error(err) {
  console.error(`ERROR(${err.code}): ${err.message}`);
}

options = {
  enableHighAccuracy: true,
  timeout: 0,
  maximumAge: 0,
};

id = navigator.geolocation.watchPosition(success, error, options);