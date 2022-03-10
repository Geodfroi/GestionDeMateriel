// ###################
// ### 2022.01.07 ###
// #################

//#region functions

async function fetchNewKey() {
  resp = await fetch("/regen");
  return await resp.text();
}

function set_delete_modal(modal, json, id) {
  modal.querySelector(".modal-label").innerText =
    "Delete [" + json.service + "] ?";
  modal.querySelector(".btn-primary").setAttribute("href", "/delete/" + id);
}

async function set_create_modal(modal) {
  inputs["password"] = await fetchNewKey();
  setInputs(inputs);
  setupModal(modal, "Create new entry", "Create", {
    mode: "create",
  });
}

function set_update_modal(modal, json) {
  setInputs(inputs, json);
  setupModal(modal, `Update [${json.service}] entry`, "Update", {
    mode: "update",
  });
}

function packFormData(e) {
  return {
    mode: e.target.getAttribute("mode"),
    entry: getInputs(inputs),
  };
}

function handleFeedback(json) {
  setWarnings(json);
}

//#endregion

inputs = { id: 0, email: "", password: "", service: "", username: "" };

hookTableModal("delete-modal", (modal, id) => {
  queryID(id).then((json) => set_delete_modal(modal, json, id));
});

hookTableModal("edit-modal", (modal, id) => {
  if (id) {
    queryID(id).then((json) => set_update_modal(modal, json));
  } else {
    set_create_modal(modal);
  }
});

hookBtn("regen-key-btn", async () => {
  let key = await fetchNewKey();
  setInput("password", key, true);
});
hookBtn("edit-submit-btn", (e) => submitForm(e, packFormData, handleFeedback));
