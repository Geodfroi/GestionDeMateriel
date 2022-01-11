// ################################
// ## Joël Piguet - 2022.01.11 ###
// ##############################

/**
 * Clear input value.
 *
 * @param {*} json_key
 */
function clearInputValues() {
  for (let i = 0; i < arguments.length; i++) {
    let json_key = arguments[i];
    displayInputValue(json_key, "");
  }
}

/**
 * Clear valid/invalid tag from form inputs.
 */
function clearWarning() {
  for (let i = 0; i < arguments.length; i++) {
    let json_key = arguments[i];
    let input_id = "form-" + json_key;
    let input = document.getElementById(input_id);
    input.classList.remove("is-invalid");
    input.classList.remove("is-valid");
  }
}

/**
 * Display input value.
 *
 * @param {*} json_key
 * @param {*} value
 */
function displayInputValue(json_key, value) {
  let input_id = "form-" + json_key;
  document.getElementById(input_id).value = htmlEntities(value);
}

/**
 * Show inputs as valid or Invalid; display warnings under input fields when the submitted input was invalid.
 *
 * @param {*} json json fetch response.
 * @param {*} input_id
 * @param {*} json_key
 */
function displayWarnings(json) {
  for (let i = 0; i < arguments.length; i++) {
    let json_key = arguments[i];
    let input_id = "form-" + json_key;
    let input_feedback_id = input_id + "-feedback";

    let input = document.getElementById(input_id);
    let feedback_element = document.getElementById(input_feedback_id);

    input.classList.remove("is-invalid");
    input.classList.remove("is-valid");

    let warning = getJSONWarning(json, json_key);

    if (warning) {
      input.classList.add("is-invalid");
      feedback_element.innerText = warning;
    } else {
      input.classList.add("is-valid");
    }
  }
}

/**
 * Return warning from json fetch response.
 *
 * @param {*} json
 * @param {*} input_key
 * @returns Warning from input field as string.
 */
function getJSONWarning(json, input_key) {
  if ("warnings" in json) {
    if (input_key in json.warnings) {
      return json.warnings[input_key];
    }
  }
  return false;
}

/**
 * Return json from given keys
 */
function getFormValues() {
  let json = {};
  console.log("getFormValues");

  for (let i = 0; i < arguments.length; i++) {
    let json_key = arguments[i];
    let input_id = "form-" + json_key;
    let input = document.getElementById(input_id);

    json[json_key] = htmlEntities(input.value.trim());
  }
  console.log(json);
  return json;
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
