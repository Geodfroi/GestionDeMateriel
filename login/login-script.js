// ################################
// ## Joël Piguet - 2022.04.05 ###
// ##############################

function callback(json) {
  displayWarnings(json, "login", "password");

  if (json.display_renew_btn) {
    document.getElementById("rewew-div").removeAttribute("hidden");
    const btn = document.getElementById("renew-link");
    const href = `${page_url}?forgottenpassword=${json.login}`;
    btn.setAttribute("href", href);
    btn.innerText = `Envoyer un nouveau mot de passe à ${json.login} ?`;
  }
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

function submit_login() {
  data = getFormValues(["login", "password"]);
  postReceiveJSON("submit-login", callback, data);
}

hookBtn("submit-btn", submit_login);
hookBtn("show-password-btn", showPassword);
