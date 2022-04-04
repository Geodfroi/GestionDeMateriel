// ################################
// ## Joël Piguet - 2022.04.04 ###
// ##############################

function callback(json) {
  displayWarnings(json, "login", "password");

  if (json.display_renew_btn) {
    console.log("display");
    document.getElementById("rewew-div").removeAttribute("hidden");
    let btn = document.getElementById("renew-link");

    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + json.login);
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
  postRequest("submit-login", callback, data);
}

hookBtn("submit-btn", submit_login);
hookBtn("show-password-btn", showPassword);
