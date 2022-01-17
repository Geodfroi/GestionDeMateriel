// ################################
// ## Joël Piguet - 2022.01.17 ###
// ##############################

const ART_TABLE = "/articlesTable";

const DATE_BEFORE = "before-peremption";
const DATE_AFTER = "after-peremption";

/**
 * Set header links depending on orderby value.
 */
function setHeaderLinks() {
  let table = document.getElementById("table");
  let orderby = table.getAttribute("orderby");

  const article_header = document.getElementById("article-header");
  const location_header = document.getElementById("location-header");
  const date_header = document.getElementById("per-date-header");
  const owner_header = document.getElementById("owner-header");

  let root_href = `${ART_TABLE}?orderby=`;

  article_header
    .querySelector("a")
    .setAttribute(
      "href",
      orderby === "NAME_ASC" ? `${root_href}NAME_DESC` : `${root_href}NAME_ASC`
    );

  location_header
    .querySelector("a")
    .setAttribute(
      "href",
      orderby === "LOCATION_ASC"
        ? `${root_href}LOCATION_DESC`
        : `${root_href}LOCATION_ASC`
    );

  date_header
    .querySelector("a")
    .setAttribute(
      "href",
      orderby === "DELAY_DESC"
        ? `${root_href}DELAY_ASC`
        : `${root_href}DELAY_DESC`
    );

  owner_header
    .querySelector("a")
    .setAttribute(
      "href",
      orderby === "OWNED_BY_DESC"
        ? `${root_href}OWNED_BY_ASC`
        : `${root_href}OWNED_BY_DESC`
    );
}

/**
 * Display caret icon besides table header to show orderby value.
 */
function displayCarets() {
  let table = document.getElementById("table");
  let orderby = table.getAttribute("orderby");

  const article_header = document.getElementById("article-header");
  const location_header = document.getElementById("location-header");
  const date_header = document.getElementById("per-date-header");
  const owner_header = document.getElementById("owner-header");

  // display carets
  if (orderby === "NAME_ASC") {
    article_header.querySelector("span").classList.add("bi-caret-down");
  } else if (orderby === "NAME_DESC") {
    article_header.querySelector("span").classList.add("bi-caret-up");
  }

  if (orderby === "LOCATION_ASC") {
    location_header.querySelector("span").classList.add("bi-caret-down");
  } else if (orderby === "LOCATION_DESC") {
    location_header.querySelector("span").classList.add("bi-caret-up");
  }

  if (orderby === "DELAY_ASC") {
    date_header.querySelector("span").classList.add("bi-caret-up");
  } else if (orderby === "DELAY_DESC") {
    date_header.querySelector("span").classList.add("bi-caret-down");
  }

  if (orderby === "OWNED_BY_DESC") {
    owner_header.querySelector("span").classList.add("bi-caret-up");
  } else if (orderby === "OWNED_BY_ASC") {
    owner_header.querySelector("span").classList.add("bi-caret-down");
  }
}

function displayCount() {
  let nav = document.getElementById("display-nav");
  let count = nav.getAttribute("display-count");

  // show selection
  let id = `display-${count}`;
  let selection_link = document.getElementById(id);
  selection_link.setAttribute("active", true);
  selection_link.classList.add("text-secondary");
  selection_link.classList.add("text-decoration-underline");
}

/**
 * Return formatted date.
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

/**
 * Display current applied filters.
 */
function displayFilter() {
  let filter_str = document
    .getElementById("filter-data")
    .getAttribute("filter");
  let obj = JSON.parse(filter_str);

  console.log(obj);

  let str = "Filters";
  if (obj.name) {
    str += ` [article: ${obj.name}]`;
  }

  if (obj.location) {
    str += ` [location: ${obj.location}]`;
  }

  if (obj.before_peremption) {
    let peremption_date = toDate(obj.before_peremption);
    str += ` [péremption avant le: ${frenchFormat(peremption_date)}]`;
  }

  if (obj.after_peremption) {
    let peremption_date = toDate(obj.after_peremption);
    str += ` [péremption après le: ${frenchFormat(peremption_date)}]`;
  }

  if (obj.show_expired) {
    str += " [articles périmés inclus]";
  }

  document.getElementById("filter-label").innerText = str;
}

// Clear date filter btn
hookBtn(
  "filter-date-clear",
  () => (document.getElementById("filter-date-val").value = "")
);

// set date filter btn inner text and fill date type input value.
hookBtnCollection("filter-date-select", (e, element) => {
  //change btn label.
  let btn = document.getElementById("filter-date-btn");
  btn.innerText = element.innerText;

  // set hidden post value.
  let date_input = document.getElementById("filter-date-type");
  if (element.id === "filter-date-before") {
    date_input.value = DATE_BEFORE;
  } else {
    date_input.value = DATE_AFTER;
  }
});

let collection = document.getElementsByClassName("filter-date-select");
for (let index = 0; index < collection.length; index++) {
  const element = collection[index];
  element.addEventListener("click", (e) => {});
}

//fill in delete article modal info when called.
hookModalShown("delete-modal", (e, modal) => {
  if (e.relatedTarget) {
    let button = e.relatedTarget;
    let id = button.getAttribute("data-bs-id");
    let article_name = button.getAttribute("data-bs-name");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer l'article [${article_name}] ?`;

    const btn = modal.querySelector(".btn-primary");
    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + id);
  }
});

// hookModalShown("filter-modal", (e, modal) => {
//   // modal.querySelector("#filter-name").;
// });

displayCarets();
displayCount();
displayPage(ART_TABLE);
setHeaderLinks();
displayFilter();
