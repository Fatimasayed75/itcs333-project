const ADMIN_USER_ID = 62;
const GUEST_USER_ID = 0;

function IsAdmin() {
  let id = document.getElementById("user-id").value;
  // console.log("User ID: ", id);
  return id == ADMIN_USER_ID;
}

function IsGuest() {
  let id = document.getElementById("user-id").value;
  // console.log("User ID: ", id);
  return id == GUEST_USER_ID;
}