// ################################
// ## Joël Piguet - 2022.01.31 ###
// ##############################

PROFILE_ROUTE = "/profile";

const delays = ["3", "7", "14", "30"];

/**
 * fetch and display user alias when modal is opened.
 */
function aliasModalShown() {
  postRequest("get-user", (json) => {
    clearWarnings("alias");
    displayInputValue(json, "alias");
  });
}

/**
 * Fetch and display user contact email when modal is opened.
 */
function contactModalShown() {
  postRequest("get-user", (json) => {
    clearWarnings("contact-email");
    displayInputValue(json, "contact-email");
  });
}

/**
 * Fetch and display user delay options when modal is opened.
 */
function delayModalShown() {
  postRequest("get-user", (json) => {
    clearWarnings("delay-3", "delay-7", "delay-14", "delay-30");
    let json_delays = json["contact-delay"].split("-");
    for (let index = 0; index < 4; index++) {
      const key = delays[index];
      let input_id = "delay-" + key;
      setCheckboxValue(input_id, json_delays.includes(key));
    }
  });
}

function deleteAlias() {
  postRequest("update-alias", null, { alias: "" });
}

function deleteContactEmail() {
  postRequest("update-contact-email", null, { "contact-email": "" });
}

function displayDelayWarnings(json) {
  let warning = getJSONWarning(json, "delay");
  if (warning) {
    setValidTag("delay-3", false);
    setValidTag("delay-7", false);
    setValidTag("delay-14", false);
    setValidTag("delay-30", false);
    setFeedback("delay", warning);
  }
}

function getFormDelays() {
  let checked_array = [];
  for (let index = 0; index < delays.length; index++) {
    const form_id = "delay-" + delays[index];
    if (getCheckboxValue(form_id)) {
      checked_array.push(delays[index]);
    }
  }
  let json = { delay: checked_array.join("-") };
  console.log("getFormDelays");
  console.log(json);
  return json;
}

/**
 * Clear password fields when modal is opened.
 */
function passwordModalShown() {
  postRequest("get-user", () => {
    clearWarningsAndInputs("password", "password-repeat");
  });
}

/**
 * Post alias change
 */
function submitAlias() {
  postRequest(
    "update-alias",
    (json) => displayWarnings(json, "alias"),
    getFormValues(["alias"])
  );
}

/**
 * Post contact email change
 */
function submitContactEmail() {
  postRequest(
    "update-contact-email",
    (json) => displayWarnings(json, "contact-email"),
    getFormValues(["contact-email"])
  );
}

/**
 * Post peremption delay change
 */
function submitDelay() {
  postRequest(
    "update-delay",
    (json) => displayDelayWarnings(json),
    getFormDelays()
  );
}

/**
 * Post password change
 */
function submitPassword() {
  postRequest(
    "update-password",
    (json) => displayWarnings(json, "password", "password-repeat"),
    getFormValues(["password", "password-repeat"])
  );
}

hookBtn("alias-submit", submitAlias);
hookBtn("password-submit", submitPassword);
hookBtn("contact-submit", submitContactEmail);
hookBtn("delay-submit", submitDelay);

hookBtn("alias-erase", deleteAlias);
hookBtn("contact-erase", deleteContactEmail);

hookModalShown("alias-modal", aliasModalShown);
hookModalShown("password-modal", passwordModalShown);
hookModalShown("contact-modal", contactModalShown);
hookModalShown("delay-modal", delayModalShown);
