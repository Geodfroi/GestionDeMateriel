// ################################
// ## Joël Piguet - 2022.01.16 ###
// ##############################

function getFormData() {
  let json = getFormValues("login-email", "password");
  json["is-admin"] = getCheckboxValue("is-admin");
  return json;
}

function handleValidation(json) {
  displayWarnings(json, "login-email", "password");
  if (json.validated) {
    // call confirm modal
    showModal("create-modal");
  }
}

hookBtnClicked("regen-password", () => {
  call("regen-password", (json) => {
    displayInputValue("password", json.password);
  });
});

//two step process: the confirmation modal is only shown if the form is properly validated.
hookBtnClicked("add-btn", () => {
  call("validate-user", handleValidation, getFormData);
});

// post new user
hookBtnClicked("submit-btn", () => {
  call("add-user", null, getFormData);
});
