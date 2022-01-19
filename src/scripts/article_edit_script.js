// ################################
// ## Joël Piguet - 2022.01.19 ###
// ##############################

/**
 * Customize form if it is used to edit an article instead of creating a new one.
 */
function customizeForm() {
  let article = json_data.article;
  if (article) {
    document.getElementById("form-label").innerText = "Modifier l'article.";
    document.getElementById("submit-btn").innerText = "Modifier";

    displayInputValues(
      article,
      "article-name",
      "expiration-date",
      "location",
      "comments"
    );
  }
}

function getFormData() {
  let json = getFormValues(
    "article-name",
    "expiration-date",
    "location",
    "comments"
  );
  if (json_data.article) {
    json.id = json_data.article.id;
  }
  return json;
}

function handleValidation(json) {
  displayWarnings(
    json,
    "article-name",
    "expiration-date",
    "location",
    "comments"
  );
}

hookBtn("submit-btn", () => {
  let article = json_data.article;
  let call_id = article ? "update-article" : "add-article";

  call(call_id, handleValidation, getFormData);
});

// set location input value from location presets
hookBtnCollection(
  "loc-preset",
  (_, btn) => (document.getElementById("location").value = btn.innerText)
);

customizeForm();
