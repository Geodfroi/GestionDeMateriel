// ################################
// ## Joël Piguet - 2022.04.05 ###
// ##############################

function getFormData() {
  const json = getFormValues(["login-email", "password"]);
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
  postReceiveJSON("regen-password", (json) => {
    displayInputValue(json, "password");
  });
}

function postUser() {
  post("add-user", getFormData());
  window.location.replace(`${root_url}usertable`);
}

/**
 * Validate user data before calling user create confirmation modal.
 */
function validateUser() {
  postReceiveJSON("validate-user", handleValidation, getFormData());
}

hookBtn("regen-password", regenPassword);
hookBtn("submit-btn", postUser);

hookBtnCollection("add-btn", validateUser);
