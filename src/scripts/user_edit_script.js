// ################################
// ## Joël Piguet - 2022.01.31 ###
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
  postRequest("regen-password", (json) => {
    displayInputValue(json, "password");
  });
}

function postUser() {
  postRequest("add-user", null, getFormData());
}

/**
 * Validate user data before calling user create confirmation modal.
 */
function validateUser() {
  postRequest("validate-user", handleValidation, getFormData());
}

hookBtn("regen-password", regenPassword);
hookBtn("submit-btn", postUser);

hookBtnCollection("add-btn", validateUser);
