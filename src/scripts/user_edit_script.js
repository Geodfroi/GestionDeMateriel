// ################################
// ## Joël Piguet - 2022.01.13 ###
// ##############################

function getFormData() {
  let json = getFormValues("login-email", "password");
  json["is-admin"] = getCheckboxValue("is-admin");
  return json;
}

hookBtnClicked("regen-password", () => {
  call("regen-password", (json) => {
    displayInputValue("form-password", json.password);
  });
});

// post new user
hookBtnClicked("new-user-submit", () => {
  call(
    "new-user",
    (json) => displayWarnings(json, "login-email", "password"),
    getFormData()
  );
});
