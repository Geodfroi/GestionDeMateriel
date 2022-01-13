// ################################
// ## Joël Piguet - 2022.01.13 ###
// ##############################

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
 * @param {*} input_id
 * @param {*} value
 */
function displayInputValue(input_id, value) {
  document.getElementById(input_id).value = htmlEntities(value);
}

/**
 * Show inputs as valid or Invalid; display warnings under input fields when the submitted input was invalid.
 *
 * @param {*} json json fetch response.
 */
function displayWarnings(json) {
  for (let i = 1; i < arguments.length; i++) {
    let input_id = arguments[i];
    let warning = getJSONWarning(json, input_id);

    setValidTag(input_id, !warning);
    setFeedback(input_id, warning);
  }
  console.log("displayewd");
}

function setFeedback(feedback_id, feedback) {
  if (!feedback) {
    return;
  }

  if (!feedback_id.endsWith("-feedback")) {
    feedback_id = feedback_id + "-feedback";
  }
  let feedback_element = document.getElementById(feedback_id);
  feedback_element.innerText = feedback;
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

    json[input_id] = htmlEntities(input.value.trim());
  }
  console.log(json);
  return json;
}

/**
 * Listen to btn click event, suppress normal action and execute defined callback.
 *
 * @param {*} id Btn id
 * @param {*} callback
 */
function hookBtnSubmit(id, callback) {
  document.getElementById(id).addEventListener("click", (e) => {
    e.preventDefault();
    callback();
  });
}

/**
 * Execute callback when the modal is shown. Used to customize form before presenting it to user.
 * @param {*} id
 * @param {*} callback
 */
function hookModalShown(id, callback) {
  document.getElementById(id).addEventListener("show.bs.modal", (e) => {
    callback();
  });
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

/**
 * Use javascript fetch ajax method to post data to the server and handle the server response.
 *
 * @param {*} req request identifier.
 * @param {*} callback function handling the server response to the fetch request.
 * @param {*} compilePostData function yielding the json data package to sent to server.
 */
function postData(req, callback, compilePostData = null) {
  let data = compilePostData == null ? {} : compilePostData();
  data["req"] = req;
  console.log(data);

  const options = {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  };

  fetch("/data", options)
    .then((res) => res.json())
    .then((json) => {
      if ("url" in json) {
        window.location.replace(json.url);
        return;
      }
      callback(json);
    });
}

function getCheckboxValue(id) {
  let element = document.getElementById(id);
  return element.checked;
}

function setCheckboxValue(id, status) {
  let element = document.getElementById(id);
  // console.log("id: " + id);
  // console.log(element);
  element.checked = status;
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
