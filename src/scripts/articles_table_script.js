// ################################
// ## Joël Piguet - 2022.03.14 ###
// ##############################

const DATE_BEFORE = "before";
const DATE_AFTER = "after";

function clearFilter() {
  document.getElementById("filter-date-val").value = "";
}

/**
 * Display caret icon besides table header to show orderby value.
 */
function displayCarets() {
  let orderby = display_data.orderby;

  const article_header = document.getElementById("article-header");
  const location_header = document.getElementById("location-header");
  const date_header = document.getElementById("per-date-header");
  const owner_header = document.getElementById("owner-header");

  // display carets
  if (orderby === "NAME_ASC") {
    article_header.querySelector(".icon").classList.add("bi-caret-down");
  } else if (orderby === "NAME_DESC") {
    article_header.querySelector(".icon").classList.add("bi-caret-up");
  }

  if (orderby === "LOCATION_ASC") {
    location_header.querySelector(".icon").classList.add("bi-caret-down");
  } else if (orderby === "LOCATION_DESC") {
    location_header.querySelector(".icon").classList.add("bi-caret-up");
  }

  if (orderby === "DELAY_ASC") {
    date_header.querySelector(".icon").classList.add("bi-caret-up");
  } else if (orderby === "DELAY_DESC") {
    date_header.querySelector(".icon").classList.add("bi-caret-down");
  }

  if (orderby === "OWNED_BY_DESC") {
    owner_header.querySelector(".icon").classList.add("bi-caret-up");
  } else if (orderby === "OWNED_BY_ASC") {
    owner_header.querySelector(".icon").classList.add("bi-caret-down");
  }
}

/**
 * Configure display of item count navbar.
 */
function displayCountNavbar() {
  let count = display_data.display_count;

  // show selection
  let id = `display-${count}`;
  let selection_link = document.getElementById(id);
  selection_link.setAttribute("active", true);
  selection_link.classList.add("text-secondary");
  selection_link.classList.add("text-decoration-underline");
}

/**
 * Display current applied filters.
 */
function displayFilter() {
  let filters = display_data.filters;

  let str = "Filtres";
  if (filters.name) {
    str += ` [article: ${filters.name}]`;
  }

  if (filters.location) {
    str += ` [location: ${filters.location}]`;
  }

  if (filters.peremption_value && filters.peremption_type) {
    let date = toDate(filters.peremption_value);
    if (filters.peremption_type == DATE_BEFORE) {
      str += ` [péremption avant le: ${frenchFormat(date)}]`;
    } else if (filters.peremption_type == DATE_AFTER) {
      str += ` [péremption après le: ${frenchFormat(date)}]`;
    }
  }

  if (filters.show_expired) {
    str += " [articles périmés inclus]";
  }

  document.getElementById("filter-label").innerText = str;
}

function deleteModalShown(e, modal) {
  if (e.relatedTarget) {
    let id = getParentAttribute(e.relatedTarget, "data-bs-id");
    let article_name = getParentAttribute(e.relatedTarget, "data-bs-name");

    modal.querySelector(
      ".modal-body"
    ).innerText = `Voulez-vous vraiment supprimer l'article [${article_name}] ?`;

    const btn = modal.querySelector(".btn-primary");
    let href_start = btn.getAttribute("href-start");
    btn.setAttribute("href", href_start + id);
  }
}

function filterModalShown(_, modal) {
  let filters = display_data.filters;

  // name
  modal.querySelector("#filter-name").value = filters.name ? filters.name : "";

  // location
  modal.querySelector("#filter-location").value = filters.location
    ? filters.location
    : "";

  // peremption date
  let date_defined = filters.peremption_type && filters.peremption_value;
  let is_before = date_defined && filters.peremption_type === DATE_BEFORE;

  setFilterDropdownLabel(
    is_before ? "Péremption avant le" : "Péremption après le"
  );

  modal.querySelector("#filter-date-type").value = is_before
    ? DATE_BEFORE
    : DATE_AFTER;

  if (filters.peremption_value) {
    modal.querySelector("#filter-date-val").value = filters.peremption_value;
  }

  // show expired
  modal.querySelector("#filter-show-expired").checked = filters.show_expired;
}

/**
 * Set header links depending on orderby value.
 */
function setHeaderLinks() {
  let orderby = display_data.orderby;

  const article_header = document.getElementById("article-header");
  const location_header = document.getElementById("location-header");
  const date_header = document.getElementById("per-date-header");
  const owner_header = document.getElementById("owner-header");

  let root_href = `${page_url}?orderby=`;

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
      orderby === "OWNED_BY_ASC"
        ? `${root_href}OWNED_BY_DESC`
        : `${root_href}OWNED_BY_ASC`
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
    let article_name = row.getAttribute("data-bs-name");
    let article_id = row.getAttribute("data-bs-id");

    modal.setAttribute("data-bs-id", article_id);
    modal.setAttribute("data-bs-name", article_name);

    modal.querySelector(
      ".modal-header span"
    ).innerText = `Modifier [${article_name}]`;

    let update_ref = row.querySelector("#update-link").getAttribute("href");
    modal.querySelector("#update-btn").setAttribute("href", update_ref);
  });
}

/**
 * Set date filter btn inner text and fill date type input value.
 *
 * @param {*} e
 * @param {*} element
 */
function setDateFilter(_, element) {
  //change btn label.
  setFilterDropdownLabel(element.innerText);
  // set hidden post value.
  let date_input = document.getElementById("filter-date-type");
  if (element.getAttribute("filter-value") === "filter-date-before") {
    date_input.value = DATE_BEFORE;
  } else {
    date_input.value = DATE_AFTER;
  }
}

/**
 * Display date filter dropdown inner text.
 *
 * @param {*} label
 */
function setFilterDropdownLabel(label) {
  let btns = document.getElementsByClassName("filter-date-dropdown");
  for (let index = 0; index < btns.length; index++) {
    const btn = btns[index];
    btn.innerText = label;
  }
}

hookBtnCollection("clear-filter", clearFilter);
hookBtnCollection("filter-dropdown-item", setDateFilter);
hookBtnCollection("table-row", selectRow, false);

hookModalShown("delete-modal", deleteModalShown);
hookModalShown("filter-modal", filterModalShown);

displayCarets();
displayCountNavbar();
displayPageNavbar();
setHeaderLinks();
displayFilter();
