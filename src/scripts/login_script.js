// ################################
// ## Joël Piguet - 2022.01.19 ###
// ##############################

function callback(json) {
  displayWarnings(json, "login", "password");

  if (json.display_renew_btn) {
    document.getElementById("rewew-div").removeAttribute("hidden");
    let btn = document.getElementById("renew-link");

    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + json.login);
    btn.innerText = `Envoyer un nouveau mot de passe à ${json.login} ?`;
  }
}

function compileData() {
  return getFormValues(["login", "password"]);
}

function showPassword(_, btn) {
  const input = document.getElementById("password");
  const img = btn.querySelector("i");

  if (input.type == "password") {
    input.type = "text";
    img.classList.remove("bi-eye");
    img.classList.add("bi-eye-slash");
  } else {
    input.type = "password";
    img.classList.remove("bi-eye-slash");
    img.classList.add("bi-eye");
  }
}

hookBtn("submit-btn", () => {
  call("submit-login", callback, compileData);
});

hookBtn("show-password-btn", showPassword);
