window.addEventListener("load", function () {
  var fields = document.querySelectorAll(".children");
  var elementsToHide = document.querySelectorAll(".children_details");
  var resChildrenEmptyVal = document.getElementById("empty-val");

  fields.forEach(function (field) {
    field.addEventListener("change", function () {
      if (field.value === "non") {
        elementsToHide.forEach(function (e) {
          e.style.display = "none";
        });
      } else if (field.value === "oui") {
        elementsToHide.forEach(function (e) {
          e.style.display = "inline-block";
        });
      } else {
        elementsToHide.forEach(function (e) {
          e.style.display = "none";
        });
      }
    });
  });
  //manage fields about spouse
  var spouse_marital_status = document.querySelectorAll(".marital_status");
  var spouseFieldsToHide = document.querySelector(".response-about-status");
  console.log("élements à cacher :", spouseFieldsToHide);
  spouse_marital_status.forEach(function (spousefield) {
    spousefield.addEventListener("change", function () {
      if (spousefield.value === "non") {
        spouseFieldsToHide.style.display = "none";
      } else if (spousefield.value === "oui") {
        spouseFieldsToHide.style.display = "inline-block";
      } else {
        spouseFieldsToHide.style.display = "none";
      }
    });
  });
});
window.onload = function() {
  var emailSuccessDiv = document.querySelector(".email-success");
  emailSuccessDiv.classList.add("show");
  setTimeout(function () {
    emailSuccessDiv.classList.remove("show");
  }, 3000);
};
