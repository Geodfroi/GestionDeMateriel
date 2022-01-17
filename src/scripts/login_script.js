// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

function callback(json) {
  console.log(json);
  displayWarnings(json, "email", "password");

  if (!getJSONWarning(json, "email")) {
    document.getElementById("rewew-div").removeAttribute("hidden");
  }
}

function compileData() {
  return getFormValues("email", "password");
}

hookBtn("submit-btn", () => {
  call("submit-login", callback, compileData);
});
