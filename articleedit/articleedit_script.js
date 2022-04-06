// ################################
// ## Joël Piguet - 2022.04.05 ###
// ##############################

/**
 * Customize form if it is used to edit an article instead of creating a new one.
 */
function customizeForm() {
  if (article) {
    document.getElementById("form-label").innerText = "Modifier l'article.";

    const btns = document.getElementsByClassName("submit-btn");
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
  const call_id = article ? "update-article" : "add-article";
  const data = getFormValues([
    "article-name",
    "expiration-date",
    "location",
    "comments",
  ]);
  if (article) {
    data.id = article.id;
  }

  postReceiveJSON(call_id, handleValidation, data);
}

hookBtnCollection("submit-btn", submitArticle);
hookBtnCollection("loc-preset", setLocationInput);

customizeForm();
