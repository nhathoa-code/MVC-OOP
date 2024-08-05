export function uniqueID() {
  var timestamp = new Date().getTime();
  var uniqueID = timestamp + Math.floor(Math.random() * 1000);
  return uniqueID;
}
