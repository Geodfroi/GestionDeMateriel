// ################################
// ## Joël Piguet - 2022.01.10 ###
// ##############################

ARTICLES_ROUTE = "/articlesTable";

// script for date filter

// set date filter btn inner text and fill date type input value.
let btn = document.getElementById("filter-date-btn");
let date_input = document.getElementById("filter-date-type");

let collection = document.getElementsByClassName("filter-date-select");
for (let index = 0; index < collection.length; index++) {
  const element = collection[index];
  element.addEventListener("click", (e) => {
    //change btn label.
    btn.innerText = element.innerText;
    // set hidden post value.
    if (element.id === "filter-date-before") {
      date_input.value = "<?php echo ArtFilter::DATE_BEFORE ?>";
    } else {
      date_input.value = "<?php echo ArtFilter::DATE_AFTER ?>";
    }
  });
}

// Clear date filter btn
document.getElementById("filter-date-clear").addEventListener("click", (e) => {
  e.preventDefault();
  document.getElementById("filter-date-val").value = "";
});

//fill in delete article modal info when called.
const delete_modal = document.getElementById("delete-modal");
delete_modal.addEventListener("show.bs.modal", (e) => {
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let article_name = button.getAttribute("data-bs-name");

    delete_modal.querySelector(".modal-body").innerText =
      "Voulez-vous vraiment supprimer l'article [ " + article_name + "] ?";

    delete_modal
      .querySelector(".btn-primary")
      .setAttribute("href", ARTICLES_ROUTE + "?delete=" + id);
  }
});
