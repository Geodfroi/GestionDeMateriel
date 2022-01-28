// ################################
// ## Joël Piguet - 2022.01.16 ###
// ##############################

function getFormData() {
  let json = getFormValues(["login-email", "password"]);
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

hookBtn("regen-password", () => {
  call("regen-password", (json) => {
    displayInputValue(json, "password");
  });
});

//two step process: the confirmation modal is only shown if the form is properly validated.
hookBtn("add-btn", () => {
  call("validate-user", handleValidation, getFormData);
});

// post new user
hookBtn("submit-btn", () => {
  call("add-user", null, getFormData);
});
