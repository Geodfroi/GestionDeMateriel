// ################################
// ## Joël Piguet - 2022.01.13 ###
// ##############################

PROFILE_ROUTE = "/profile";

const delays = ["3", "7", "14", "30"];

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

// post alias change
hookBtnSubmit("alias-submit", () => {
  postData(
    "update-alias",
    (json) => displayWarnings(json, "alias"),
    () => getFormValues("alias")
  );
});

// post alias change
hookBtnSubmit("password-submit", () => {
  postData(
    "update-password",
    (json) => displayWarnings(json, "password", "password-repeat"),
    () => getFormValues("password", "password-repeat")
  );
});

hookBtnSubmit("contact-submit", () => {
  postData(
    "update-contact-email",
    (json) => displayWarnings(json, "contact-email"),
    () => getFormValues("contact-email")
  );
});

hookBtnSubmit("delay-submit", () => {
  postData(
    "update-delay",
    (json) => displayDelayWarnings(json),
    () => getFormDelays()
  );
});

// fetch and display user alias when modal is opened.
hookModalShown("alias-modal", () => {
  postData("get-user", (json) => {
    clearWarnings("alias");
    displayInputValue("alias", json.alias);
  });
});

// clear password fields when modal is opened.
hookModalShown("password-modal", () => {
  postData("get-user", () => {
    clearWarningsAndInputs("password", "password-repeat");
  });
});

// clear password fields when modal is opened.
hookModalShown("contact-modal", () => {
  postData("get-user", (json) => {
    clearWarnings("contact-email");
    displayInputValue("contact-email", json.contact_email);
  });
});

// clear password fields when modal is opened.
hookModalShown("delay-modal", () => {
  postData("get-user", (json) => {
    clearWarnings("delay-3", "delay-7", "delay-14", "delay-30");
    let json_delays = json.contact_delay.split("-");
    for (let index = 0; index < 4; index++) {
      const key = delays[index];
      let input_id = "delay-" + key;
      setCheckboxValue(input_id, json_delays.includes(key));
    }
  });
});
