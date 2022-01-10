// ################################
// ## Joël Piguet - 2022.01.10 ###
// ##############################

USER_ROUTE = "/usersTable";

//fill in delete modal info when called.
const delete_modal = document.getElementById("delete-modal");
delete_modal.addEventListener("show.bs.modal", (e) => {
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let email = button.getAttribute("data-bs-email");

    delete_modal.querySelector(".modal-body").innerText =
      "Voulez-vous vraiment supprimer le compte utilisateur [ " + email + "] ?";

    delete_modal
      .querySelector(".btn-primary")
      .setAttribute("href", USER_ROUTE + "?delete=" + id);
  }
});

//fill in renew password modal info when called.
const renew_modal = document.getElementById("renew-modal");
renew_modal.addEventListener("show.bs.modal", (e) => {
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let email = button.getAttribute("data-bs-email");

    renew_modal.querySelector(".modal-body").innerText =
      "Envoyer un nouveau mot de passe à [" + email + "] ?";

    renew_modal
      .querySelector(".btn-primary")
      .setAttribute("href", USER_ROUTE + "?renew=" + id);
  }
});
