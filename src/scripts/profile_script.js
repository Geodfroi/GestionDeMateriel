// ################################
// ## Joël Piguet - 2022.01.11 ###
// ##############################

PROFILE_ROUTE = "/profile";

function getJSONWarning(json, key) {
  if ("warnings" in json) {
    console.log("warnings" in json);
    console.log("key:" + key);
    if (key in json.warnings) {
      return json.warnings[key];
    }
  }
  return false;
}

function displayWarning(json, form_id, json_key) {
  let input = document.getElementById(form_id);
  let feedback_element = document.getElementById(form_id + "-feedback");

  input.classList.remove("is-invalid");
  input.classList.remove("is-valid");

  let warning = getJSONWarning(json, json_key);
  console.log("warning: " + warning);

  if (warning) {
    input.classList.add("is-invalid");
    feedback_element.innerText = warning;
  } else {
    input.classList.add("is-valid");
  }
}

function submitUsername(e) {
  e.preventDefault();

  let data = {
    req: "post-profile-alias",
    alias: document.getElementById("form-alias").value.trim(),
  };

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
      displayWarning(json, "form-alias", "alias");
      console.dir(json);
    });

  //.then((res) => console.dir(res));
  // .then((res) => res.json());
  // .then((res) => {
  //   console.log(res);
  //   //   handleFeedback(json);
  //   // if (json.validated) {
  //   //   window.location.replace(PROFILE_ROUTE);
  //   // }
  // });
}

const btn = document.getElementById("form-username-submit");
btn.addEventListener("click", submitUsername);
