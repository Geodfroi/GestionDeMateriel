// ################################
// ## Joël Piguet - 2022.01.30 ###
// ##############################

function deleteModalShown(e, modal) {
  if (e.relatedTarget) {
    let id = getParentAttribute(e.relatedTarget, "data-bs-id");
    let content = getParentAttribute(e.relatedTarget, "data-bs-content");
    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer [${content}] ?`;

    const btn = modal.querySelector(".btn-primary");
    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + id);
  }
}

function editModalShown(e, modal) {
  let id = getParentAttribute(e.relatedTarget, "data-bs-id");

  modal.querySelector("#modal-title").innerText = id
    ? "Modifier le contenu"
    : "Nouvelle saisie";

  modal.querySelector("#submit-edit-btn").innerText = id
    ? "Modifier"
    : "Ajouter";

  if (id) {
    let content = getParentAttribute(e.relatedTarget, "data-bs-content");
    modal.querySelector("#id").value = id;
    modal.querySelector("#content").value = content;
  } else {
    modal.querySelector("#content").value = "";
  }
}

function submitEdit() {
  let preset_id = document.getElementById("id").value;
  let call_id = preset_id ? "update-loc-preset" : "add-loc-preset";

  postRequest(
    call_id,
    (json) => displayWarnings(json, "content"),
    getFormValues(["id", "content"])
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

  showModal("action-modal", (modal) => {
    let location_id = row.getAttribute("data-bs-id");
    let location_content = row.getAttribute("data-bs-content");

    modal.setAttribute("data-bs-id", location_id);
    modal.setAttribute("data-bs-content", location_content);
    modal.querySelector(
      ".modal-header span"
    ).innerText = `[${location_content}]`;
  });
}

hookBtn("submit-edit-btn", submitEdit);
hookBtnCollection("table-row", selectRow, false);

hookModalShown("delete-modal", deleteModalShown);
hookModalShown("edit-modal", editModalShown);
