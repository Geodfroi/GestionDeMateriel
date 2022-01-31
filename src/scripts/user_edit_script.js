// ################################
// ## Joël Piguet - 2022.01.30 ###
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

function regenPassword() {
  call("regen-password", (json) => {
    displayInputValue(json, "password");
  });
}

function postUser() {
  call("add-user", null, getFormData);
}

/**
 * Validate user data before calling user create confirmation modal.
 */
function validateUser() {
  call("validate-user", handleValidation, getFormData);
}

hookBtn("regen-password", regenPassword);
hookBtn("submit-btn", postUser);

hookBtnCollection("add-btn", validateUser);
