// const form = document.querySelector("form");

// form.addEventListener("submit", (e) => {
//   e.preventDefault();
//   const formData = new FormData(e.target);

//   fetch("/action.php", { body: formData, method: "post" });
// });

const deleteForms = document.querySelectorAll("form.delete");
deleteForms.forEach((form) => {
  form.addEventListener("submit", (e) => {
    if (!window.confirm("Wirklich löschen?")) e.preventDefault();
  });
});
