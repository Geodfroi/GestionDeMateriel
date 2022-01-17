// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

const DATE_BEFORE = "before-peremption";
const DATE_AFTER = "after-peremption";

function displayCount() {
  let nav = document.getElementById("display-nav");
  let count = nav.getAttribute("display-count");
  console.log(count);

  // show selection
  let id = `display-${count}`;
  let selection_link = document.getElementById(id);
  selection_link.setAttribute("active", true);
  selection_link.classList.add("text-secondary");
  selection_link.classList.add("text-decoration-underline");
}

// Clear date filter btn
hookBtn(
  "filter-date-clear",
  () => (document.getElementById("filter-date-val").value = "")
);

// set date filter btn inner text and fill date type input value.
hookBtnCollection("filter-date-select", (e, element) => {
  //change btn label.
  let btn = document.getElementById("filter-date-btn");
  btn.innerText = element.innerText;

  // set hidden post value.
  let date_input = document.getElementById("filter-date-type");
  if (element.id === "filter-date-before") {
    date_input.value = DATE_BEFORE;
  } else {
    date_input.value = DATE_AFTER;
  }
});

let collection = document.getElementsByClassName("filter-date-select");
for (let index = 0; index < collection.length; index++) {
  const element = collection[index];
  element.addEventListener("click", (e) => {});
}

//fill in delete article modal info when called.
hookModalShown("delete-modal", (e, modal) => {
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let article_name = button.getAttribute("data-bs-name");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer l'article [${article_name}] ?`;

    const btn = modal.querySelector(".btn-primary");
    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + id);
  }
});

displayCount();
