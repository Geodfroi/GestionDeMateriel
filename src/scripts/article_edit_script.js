// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

/**
 * Customize form if it is used to edit an article instead of creating a new one.
 */
function customizeForm() {
  let article_id = document.getElementById("id").value;
  if (article_id) {
    document.getElementById("form-label").innerText = "Modifier l'article.";
    document.getElementById("submit-btn").innerText = "Modifier";

    let json = { id: article_id };
    call("get-article", displayArticle, () => json);
  }
}

function displayArticle(json) {
  displayInputValues(
    json,
    "article-name",
    "expiration-date",
    "location",
    "comments"
  );
}

function getFormData() {
  return getFormValues(
    "id",
    "article-name",
    "expiration-date",
    "location",
    "comments"
  );
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
  let article_id = document.getElementById("id").value;
  let call_id = article_id ? "update-article" : "add-article";
  console.log(call_id);

  call(call_id, handleValidation, getFormData);
});

// set location input value from location presets
hookBtnCollection(
  "loc-preset",
  (_, btn) => (document.getElementById("location").value = btn.innerText)
);

customizeForm();
