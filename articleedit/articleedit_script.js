// ################################
// ## Joël Piguet - 2022.04.06 ###
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

/**
 * Display placeholder text in input fields when screen is smallsized.
 */
function displayPlaceholders() {
  const name_input = document.querySelector("#article-name");
  const location_input = document.querySelector("#location");
  if (isSmallScreen()) {
    name_input.setAttribute("placeholder", "Nom de l'article");
    location_input.setAttribute("placeholder", "Emplacement");
  } else {
    name_input.removeAttribute("placeholder");
    location_input.removeAttribute("placeholder");
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
 */
function setLocationInput(_, btn) {
  document.getElementById("location").value = btn.innerText;
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
displayPlaceholders();
breakpoints_init();

document.addEventListener("bs.breakpoint.change", () => {
  displayPlaceholders();
});
