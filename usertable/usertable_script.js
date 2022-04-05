// ################################
// ## Joël Piguet - 2022.04.05 ###
// ##############################

/**
 * Display caret icon besides table header to show orderby value.
 */
function displayCarets() {
  const orderby = display_data.orderby;

  const email_header = document.getElementById("email-header");
  const creation_header = document.getElementById("creation-header");
  const login_header = document.getElementById("last-login-header");

  if (orderby === "EMAIL_DESC") {
    email_header.querySelector(".icon").classList.add("bi-caret-up");
  } else if (orderby === "EMAIL_ASC") {
    email_header.querySelector(".icon").classList.add("bi-caret-down");
  }

  if (orderby === "CREATED_DESC") {
    creation_header.querySelector(".icon").classList.add("bi-caret-down");
  } else if (orderby === "CREATED_ASC") {
    creation_header.querySelector(".icon").classList.add("bi-caret-up");
  }

  if (orderby === "LOGIN_DESC") {
    login_header.querySelector(".icon").classList.add("bi-caret-down");
  } else if (orderby === "LOGIN_ASC") {
    login_header.querySelector(".icon").classList.add("bi-caret-up");
  }
}

function deleteModalShown(e, modal) {
  if (e.relatedTarget) {
    const id_ = getParentAttribute(e.relatedTarget, "data-bs-id");
    const email = getParentAttribute(e.relatedTarget, "data-bs-email");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer le compte utilisateur [${email}] ? `;

    const btn = modal.querySelector(".btn-primary");
    const href = `${page_url}?deleteuser=${id_}`;
    btn.setAttribute("href", href);
  }
}

function renewModalShown(e, modal) {
  if (e.relatedTarget) {
    const id_ = getParentAttribute(e.relatedTarget, "data-bs-id");
    const email = getParentAttribute(e.relatedTarget, "data-bs-email");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Envoyer un nouveau mot de passe à [${email}] ? `;

    const btn = modal.querySelector(".btn-primary");
    const href = `${page_url}?renewuserpassword=${id_}`;
    btn.setAttribute("href", href);
  }
}

/**
 * Set header links depending on orderby value.
 */
function setHeaderLinks() {
  const orderby = display_data.orderby;

  const email_header = document.getElementById("email-header");
  const creation_header = document.getElementById("creation-header");
  const login_header = document.getElementById("last-login-header");

  const root_href = `${page_url}?orderby=`;

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

/**
 * Show options modal when row is clicked if screen is in mobile mode.
 *
 * @param {*} _
 * @param {*} row
 */
function selectRow(_, row) {
  if (!isSmallScreen()) {
    return;
  }
  const is_admin = row.getAttribute("data-bs-admin");
  if (is_admin) {
    return;
  }

  showModal("action-modal", (modal) => {
    const user_id = row.getAttribute("data-bs-id");
    const user_email = row.getAttribute("data-bs-email");

    if (is_admin) {
      close;
    }

    modal.setAttribute("data-bs-id", user_id);
    modal.setAttribute("data-bs-email", user_email);

    modal.querySelector(
      ".modal-header span"
    ).innerText = `Intéragir avec [${user_email}]`;
  });
}

hookBtnCollection("table-row", selectRow, false);

hookModalShown("delete-modal", deleteModalShown);
hookModalShown("renew-modal", renewModalShown);

displayCarets();
setHeaderLinks();
displayPageNavbar();
