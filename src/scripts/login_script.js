// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

function callback(json) {
  displayWarnings(json, "email", "password");

  if (!getJSONWarning(json, "email")) {
    document.getElementById("rewew-div").removeAttribute("hidden");
    let btn = document.getElementById("renew-link");

    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + json.email);
    btn.innerText = `Envoyer un nouveau mot de passe à ${json.email} ?`;
  }
}

function compileData() {
  return getFormValues("email", "password");
}

hookBtn("submit-btn", () => {
  call("submit-login", callback, compileData);
});
