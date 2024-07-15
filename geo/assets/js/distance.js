let id;
let target;
let options;

function deg2rad(degrees) {
  return degrees * (Math.PI / 180);
}

function success(pos) {

  for (let i = 0; i < targets.length; i++) {
  var target = targets[i];

  var id = target.id;

  const crd = pos.coords;

  document.getElementById(id).innerHTML = crd.latitude;
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(crd.latitude-target.latitude);  // deg2rad below
  var dLon = deg2rad(crd.latitude-target.latitude); 
  
  var a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(deg2rad(crd.latitude)) * Math.cos(deg2rad(target.latitude)) * 
    Math.sin(dLon/2) * Math.sin(dLon/2)
    ; 
    
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
  var d = Math.round(R * c * 1000); // Distance in m
  document.getElementById(id).innerHTML = d + "m to " + target.name;

  if (d < 20000) {
    document.getElementById(id).innerHTML = "Congratulations, you reached the target <br>" + document.getElementById(id).innerHTML
    +"<button cp='"+id+"'>check-in</button>";
    //navigator.geolocation.clearWatch(id);

  }
}
}

function error(err) {
  console.error(`ERROR(${err.code}): ${err.message}`);
}

targets = [{
  latitude: 51.422391,
  longitude: -0.204278,
  id: 1,
  name: "wimbledon"
},
{
  //51.334585, -0.268582
  latitude: 51.334585,
  longitude: -0.268582,
  id: 2,
  name: "epsom"
}];
//multiple targets 51.422391, -0.204278

options = {
  enableHighAccuracy: true,
  timeout: 0,
  maximumAge: 0,
};

id = navigator.geolocation.watchPosition(success, error, options);