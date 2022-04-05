// ################################
// ## Joël Piguet - 2022.04.04 ###
// ##############################

//#region fetch

/**
 * Use javascript fetch ajax method to send a GET request. In this app, post request are made to the current page url.
 *
 * @param {*} req request identifier.
 * @param {*} json json data used to construct the GET request path.
 */
function getRequest(json = null) {
  // let url = new URL(url, window.location.origin);
  url = page_url;
  console.log("GET request at: " + url);

  json ??= {};
  let keys = Object.keys(json);
  for (let index = 0; index < keys.length; index++) {
    let key = keys[index];
    url.searchParams.append(key, json[key]);
  }

  fetch(url);
}

/**
 * Use javascript fetch ajax method to post data to the server and handle the server response. In this app, post request are made to the current page url.
 *
 * @param {*} request_name request identifier.
 * @param {*} callback function handling the server response to the fetch request.
 * @param {*} data json data package to sent to server.
 */
function postRequest(request_name, callback = null, data = null) {
  data ??= {};
  data[request_name] = true;
  const options = {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  };
  url = page_url;
  console.log(url);

  // fetch(page_url, options)
  //   .then((res) => {
  //     console.log(`${request_name} POST response`);
  //     console.dir(res);
  //     return res.text();
  //   })

  //   .then((txt) => console.log(txt));

  fetch(page_url, options)
    .then((res) => {
      // console.log(`${request_name} POST response`);
      // console.dir(res);
      console.log(res.headers);

      return res.json();
    })
    .then((json) => {
      // console.log(typeof json);
      // console.log(`${request_name} POST json`);
      // console.dir(json);
      if ("url" in json) {
        // console.log(json.url);
        window.location.replace(json.url);
        return;
      }
      if (callback != null) {
        callback(json);
      }
    });
}

//#endregion

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
 * Configure display of page navigation navbar.
 */
function displayPageNavbar() {
  let current_page = display_data.page;
  let page_count = display_data.page_count;

  const page_last = document.getElementById("page-last");
  const page_next = document.getElementById("page-next");

  page_last
    .querySelector("a")
    .setAttribute("href", `${page_url}?page=${current_page - 1}`);

  page_next
    .querySelector("a")
    .setAttribute("href", `${page_url}?page=${current_page + 1}`);

  if (current_page == 1) {
    page_last.classList.add("disabled");
  }
  if (current_page == page_count) {
    page_next.classList.add("disabled");
  }
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
  for (let i = 1; i < arguments.length; i++) {
    let input_id = arguments[i];
    let warning = getJSONWarning(json, input_id);

    setValidTag(input_id, !warning);
    setFeedback(input_id, warning);
  }
}

/**
 * Return formatted date in display format dd-mm-YYYY
 * https://stackoverflow.com/questions/23593052/format-javascript-date-as-yyyy-mm-dd
 *
 * @param {Date} date
 * @returns date in 'YYYY-mm-dd' format.
 */
function frenchFormat(date) {
  var d = new Date(date),
    month = "" + (d.getMonth() + 1),
    day = "" + d.getDate(),
    year = d.getFullYear();

  if (month.length < 2) month = "0" + month;
  if (day.length < 2) day = "0" + day;

  return [day, month, year].join("-");
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

function getFormValue(id) {
  let input = document.getElementById(id);
  if (input) {
    let type = input.getAttribute("type");
    if (type == "checkbox") {
      return input.checked ? true : null;
    }
    if (input.value) {
      return input.value.trim();
    }
  }
  return null;
}

/**
 * Return json from given keys
 *
 * @param {array} json_ids
 * @param {array} use_alternate_id alternate ids if screen is small.
 * @returns {string} json
 */
function getFormValues(array, alternate_ids = null) {
  let use_alternate_ids = alternate_ids && isSmallScreen();
  let json = {};

  for (let i = 0; i < array.length; i++) {
    let json_id = array[i];
    let input_id = use_alternate_ids ? alternate_ids[i] : json_id;
    let val = getFormValue(input_id);
    if (val) {
      json[json_id] = val;
    }
  }
  return json;
}

/**
 * Get attribute of first parent element owning that attribute.
 *
 * @param {*} element
 * @param {*} attribute
 * @returns attribute value.
 */
function getParentAttribute(element, attribute) {
  const parent = element.parentElement;
  if (parent) {
    let attr = parent.getAttribute(attribute);
    if (attr) {
      return attr;
    }
    return getParentAttribute(parent, attribute);
  }
  return null;
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
function hookBtnCollection(class_name, callback, preventDefault = true) {
  let collection = document.getElementsByClassName(class_name);

  for (let index = 0; index < collection.length; index++) {
    const element = collection[index];
    element.addEventListener("click", (e) => {
      if (preventDefault) {
        e.preventDefault();
      }
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

function isSmallScreen() {
  let currentBreakpoint = getCurrentBreakpoint();
  return (
    currentBreakpoint.name == "xs" ||
    currentBreakpoint.name == "sm" ||
    currentBreakpoint == "md"
  );
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
 * @param {*} callback To be executed before the modal is shown.
 */
function showModal(modal_id, callback = null) {
  let modal = document.getElementById(modal_id);
  if (callback) {
    callback(modal);
  }
  new bootstrap.Modal(modal).show();
}

/**
 * Create date instance from string in format YYYY-mm-dd
 *
 * @param {*} str
 * @returns {Date}
 */
function toDate(str) {
  let array = str.split("-");
  let year = parseInt(array[0]);
  let month = parseInt(array[1]) - 1;
  let day = parseInt(array[2]);

  return new Date(year, month, day);
}
