const ADMIN_USER_ID = 62;

function IsAdmin() {
  let id = document.getElementById("user-id").value;
  // console.log("User ID: ", id);
  return id == ADMIN_USER_ID;
}
