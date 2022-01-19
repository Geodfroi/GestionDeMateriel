// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

const USERS_TABLE = "/usersTable";

/**
 * Display caret icon besides table header to show orderby value.
 */
function displayCarets() {
  let orderby = json_data.display_data.orderby;

  const email_header = document.getElementById("email-header");
  const creation_header = document.getElementById("creation-header");
  const login_header = document.getElementById("last-login-header");

  if (orderby === "EMAIL_DESC") {
    email_header.querySelector("span").classList.add("bi-caret-up");
  } else if (orderby === "EMAIL_ASC") {
    email_header.querySelector("span").classList.add("bi-caret-down");
  }

  if (orderby === "CREATED_DESC") {
    creation_header.querySelector("span").classList.add("bi-caret-down");
  } else if (orderby === "CREATED_ASC") {
    creation_header.querySelector("span").classList.add("bi-caret-up");
  }

  if (orderby === "LOGIN_DESC") {
    login_header.querySelector("span").classList.add("bi-caret-down");
  } else if (orderby === "LOGIN_ASC") {
    login_header.querySelector("span").classList.add("bi-caret-up");
  }
}

/**
 * Set header links depending on orderby value.
 */
function setHeaderLinks() {
  let orderby = json_data.display_data.orderby;

  const email_header = document.getElementById("email-header");
  const creation_header = document.getElementById("creation-header");
  const login_header = document.getElementById("last-login-header");

  let root_href = `${USERS_TABLE}?orderby=`;

  email_header
    .querySelector("a")
    .setAttribute(
      "href",
      orderby === "EMAIL_ASC"
        ? `${root_href}EMAIL_DESC`
        : `${root_href}EMAIL_ASC`
    );
  creation_header
    .querySelector("a")
    .setAttribute(
      "href",
      orderby === "CREATED_DESC"
        ? `${root_href}CREATED_ASC`
        : `${root_href}CREATED_DESC`
    );
  login_header
    .querySelector("a")
    .setAttribute(
      "href",
      orderby === "LOGIN_DESC"
        ? `${root_href}LOGIN_ASC`
        : `${root_href}LOGIN_DESC`
    );
}

//fill in delete modal info when called.
hookModalShown("delete-modal", (e, modal) => {
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

displayCarets();
setHeaderLinks();
displayPageNavbar(USERS_TABLE);
