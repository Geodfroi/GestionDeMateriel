// ################################
// ## Joël Piguet - 2022.01.28 ###
// ##############################

/**
 * Customize form if it is used to edit an article instead of creating a new one.
 */
function customizeForm() {
  let article = json_data.article;
  if (article) {
    document.getElementById("form-label").innerText = "Modifier l'article.";

    let btns = document.getElementsByClassName("submit-btn");
    for (let index = 0; index < btns.length; index++) {
      const btn = btns[index];
      btn.innerText = "Modifier";
    }

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
  let json = getFormValues([
    "article-name",
    "expiration-date",
    "location",
    "comments",
  ]);
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

/**
 * Set location input value from location presets
 * @param {*} e
 * @param {*} btn
 * @returns
 */
function setLocationInput(_, btn) {
  return (document.getElementById("location").value = btn.innerText);
}

function submitArticle() {
  let article = json_data.article;
  let call_id = article ? "update-article" : "add-article";

  postRequest(call_id, handleValidation, getFormData());
}

hookBtnCollection("submit-btn", submitArticle);
hookBtnCollection("loc-preset", setLocationInput);

customizeForm();
