// ################################
// ## Joël Piguet - 2022.01.10 ###
// ##############################

// set location input value from preset
let loc_input = document.getElementById("form-location");

let collection = document.getElementsByClassName("loc-preset");
for (let index = 0; index < collection.length; index++) {
  const element = collection[index];
  element.addEventListener("click", (e) => {
    loc_input.value = element.innerText;
  });
}
