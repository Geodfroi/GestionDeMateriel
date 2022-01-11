// ################################
// ## Joël Piguet - 2022.01.11 ###
// ##############################

PROFILE_ROUTE = "/profile";

// post alias change
document.getElementById("form-alias-submit").addEventListener("click", (e) => {
  e.preventDefault();
  postData(
    "update-profile-alias",
    (json) => displayWarnings(json, "alias"),
    () => getFormValues("alias")
  );
});

// post alias change
document
  .getElementById("form-password-submit")
  .addEventListener("click", (e) => {
    e.preventDefault();
    postData(
      "update-profile-password",
      (json) => displayWarnings(json, "password", "password-repeat"),
      () => getFormValues("password", "password-repeat")
    );
  });

// fetch and display user alias when modal is opened.
document.getElementById("alias-modal").addEventListener("show.bs.modal", () => {
  postData("get-user", (json) => {
    clearWarning("form-alias");
    displayInputValue("alias", json.alias);
  });
});

// clear password fields when modal is opened.
document
  .getElementById("password-modal")
  .addEventListener("show.bs.modal", () => {
    postData("get-user", () => {
      clearWarnings("password", "password-repeat");
      clearInputValues("password", "password-repeat");
    });
  });
