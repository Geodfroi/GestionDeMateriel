// ################################
// ## Joël Piguet - 2022.04.05 ###
// ##############################

function deleteModalShown(e, modal) {
  if (e.relatedTarget) {
    const id_ = getParentAttribute(e.relatedTarget, "data-bs-id");
    const content = getParentAttribute(e.relatedTarget, "data-bs-content");
    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer [${content}] ?`;

    const btn = modal.querySelector(".btn-primary");
    const href = `${page_url}?deletelocpreset=${id_}`;
    btn.setAttribute("href", href);
  }
}

function editModalShown(e, modal) {
  const id_ = getParentAttribute(e.relatedTarget, "data-bs-id");

  modal.querySelector("#modal-title").innerText = id_
    ? "Modifier le contenu"
    : "Nouvelle saisie";

  modal.querySelector("#submit-edit-btn").innerText = id_
    ? "Modifier"
    : "Ajouter";

  if (id_) {
    const content = getParentAttribute(e.relatedTarget, "data-bs-content");
    modal.querySelector("#id").value = id_;
    modal.querySelector("#content").value = content;
  } else {
    modal.querySelector("#content").value = "";
  }
}

function submitEdit() {
  const preset_id = document.getElementById("id").value;
  const call_id = preset_id ? "update-loc-preset" : "add-loc-preset";

  postReceiveJSON(
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
    const location_id = row.getAttribute("data-bs-id");
    const location_content = row.getAttribute("data-bs-content");

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
