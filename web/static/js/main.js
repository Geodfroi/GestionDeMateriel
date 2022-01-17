// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

/**
 * Use javascript fetch ajax method to post data to the server and handle the server response.
 *
 * @param {*} req request identifier.
 * @param {*} callback function handling the server response to the fetch request.
 * @param {*} compilePostData function yielding the json data package to sent to server.
 */
function call(req, callback = null, compilePostData = null) {
  let data = compilePostData == null ? {} : compilePostData();
  data["req"] = req;

  const options = {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  };

  fetch("/request", options)
    .then((res) => {
      return res.json();
    })
    .then((json) => {
      if ("url" in json) {
        window.location.replace(json.url);
        return;
      }
      if (callback != null) {
        callback(json);
      }
    });
}

/**
 * Clear input valu.
 */
function clearInputValues() {
  for (let i = 0; i < arguments.length; i++) {
    let input_id = arguments[i];
    displayInputValue(input_id, "");
  }
}

/**
 * Clear valid/invalid tag from form inputs.
 */
function clearWarnings() {
  for (let i = 0; i < arguments.length; i++) {
    let input_id = arguments[i];
    let input = document.getElementById(input_id);
    // console.log("input_id: " + input_id);
    // console.log("input: " + input);

    input.classList.remove("is-invalid");
    input.classList.remove("is-valid");
  }
}

/**
 * Call clearWarnings and clearInputValues
 */
function clearWarningsAndInputs() {
  clearWarnings();
  clearInputValues();
}

/**
 * Display input value.
 *
 * @param {*} json
 * @param {*} input_id
 * @param {*} json_id
 */
function displayInputValue(json, json_id, input_id = null) {
  json_id = input_id = input_id != null ? input_id : json_id;
  document.getElementById(input_id).value = htmlEntities(json[json_id]);
}

/**
 * In json values and form inputs share keys, they can be displayed in only one call listing the kes to display.
 *
 * @param {*} json
 */
function displayInputValues(json) {
  for (let i = 1; i < arguments.length; i++) {
    let json_id = arguments[i];
    displayInputValue(json, json_id);
  }
}

/**
 * Show inputs as valid or Invalid; display warnings under input fields when the submitted input was invalid.
 *
 * @param {*} json json fetch response.
 */
function displayWarnings(json) {
  console.log("displayWarnings");
  console.log(json);
  for (let i = 1; i < arguments.length; i++) {
    let input_id = arguments[i];
    let warning = getJSONWarning(json, input_id);

    setValidTag(input_id, !warning);
    setFeedback(input_id, warning);
  }
}

function getCheckboxValue(id) {
  let element = document.getElementById(id);
  return element.checked;
}

/**
 * Return warning from json fetch response.
 *
 * @param {*} json
 * @param {*} key
 * @returns Warning from input field as string.
 */
function getJSONWarning(json, key) {
  if ("warnings" in json) {
    if (key in json.warnings) {
      return json.warnings[key];
    }
  }
  return false;
}

/**
 * Return json from given keys
 */
function getFormValues() {
  let json = {};

  for (let i = 0; i < arguments.length; i++) {
    let input_id = arguments[i];
    let input = document.getElementById(input_id);
    if (input) {
      json[input_id] = input.value.trim();
      // json[input_id] = htmlEntities(input.value.trim());
    }
  }
  return json;
}

/**
 * Listen to button click event by id, suppress normal action and execute defined callback.
 *
 * @param {*} id Btn id
 * @param {*} callback
 */
function hookBtn(id, callback) {
  const btn = document.getElementById(id);
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    callback(e, btn);
  });
}

/**
 * Listen to all buttons click event by class name.
 *
 * @param {*} class_name
 * @param {*} callback
 */
function hookBtnCollection(class_name, callback) {
  let collection = document.getElementsByClassName(class_name);
  for (let index = 0; index < collection.length; index++) {
    const element = collection[index];
    element.addEventListener("click", (e) => {
      e.preventDefault();
      callback(e, element);
    });
  }
}

/**
 * Execute callback when the modal is shown. Used to customize modal form before presenting it to user.
 *
 * @param {*} id
 * @param {*} callback
 */
function hookModalShown(id, callback) {
  const modal = document.getElementById(id);
  modal.addEventListener("show.bs.modal", (e) => callback(e, modal));
}

/**
 * https://css-tricks.com/snippets/javascript/htmlentities-for-javascript/
 */
function htmlEntities(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function setCheckboxValue(id, status) {
  let element = document.getElementById(id);
  element.checked = status;
}

function setFeedback(feedback_id, feedback) {
  if (!feedback) {
    return;
  }

  if (!feedback_id.endsWith("-feedback")) {
    feedback_id = feedback_id + "-feedback";
  }
  let feedback_element = document.getElementById(feedback_id);
  if (feedback_element) {
    feedback_element.innerText = feedback;
  }
}

function setValidTag(id, status) {
  let input = document.getElementById(id);
  input.classList.remove("is-invalid");
  input.classList.remove("is-valid");

  if (status) {
    input.classList.add("is-valid");
  } else {
    input.classList.add("is-invalid");
  }
}

/**
 *Open modal by id.
 *
 * @param {String} modal_id
 */
function showModal(modal_id) {
  let modal = document.getElementById(modal_id);
  new bootstrap.Modal(modal).show();
}

// function explode_to_ints(array_str) {
//   let array = array_str.split("-");
//   let array_ints = [];
//   for (let index = 0; index < array.length; index++) {
//     const element = array[index].trim();
//     array_ints.push(parseInt(element));
//   }
//   return array_ints;
// }
