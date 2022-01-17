// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

function getFormData() {}

//fill in delete location modal info when called.
hookModalShown("delete-modal", (e, modal) => {
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let content = button.getAttribute("data-bs-content");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer [${content}] ?`;

    const btn = modal.querySelector(".btn-primary");
    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + id);
  }
});

hookModalShown("edit-modal", (e, modal) => {
  let button = e.relatedTarget;
  let id = button.getAttribute("data-bs-id");

  document.getElementById("modal-title").innerText = id
    ? "Modifier le contenu"
    : "Nouvelle saisie";

  document.getElementById("submit-btn").innerText = id ? "Modifier" : "Ajouter";

  if (id) {
    let content = button.getAttribute("data-bs-content");
    document.getElementById("id").value = id;
    document.getElementById("content").value = content;
  } else {
    document.getElementById("content").value = "";
  }
});

hookBtn("submit-btn", () => {
  let preset_id = document.getElementById("id").value;
  let call_id = preset_id ? "update-loc-preset" : "add-loc-preset";
  console.log(call_id);

  call(
    call_id,
    (json) => displayWarnings(json, "content"),
    () => getFormValues("id", "content")
  );
});
