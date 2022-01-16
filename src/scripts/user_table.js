// ################################
// ## Joël Piguet - 2022.01.16 ###
// ##############################

USER_ROUTE = "/usersTable";

//fill in delete modal info when called.
hookModalShown("delete-modal", (e, modal) => {
  console.log("delete-modal");
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let email = button.getAttribute("data-bs-email");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer le compte utilisateur [${email}] ? `;

    let btn = modal.querySelector(".btn-primary");
    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + id);
  }
});

//fill in renew password modal info when called.
hookModalShown("renew-modal", (e, modal) => {
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let email = button.getAttribute("data-bs-email");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Envoyer un nouveau mot de passe à [${email}] ? `;

    let btn = modal.querySelector(".btn-primary");
    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + id);
  }
});
