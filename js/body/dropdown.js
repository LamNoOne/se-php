const sel = document.querySelector(".sel");
const label = document.querySelector(".label");
const options = document.querySelector(".options");

// options.setAttribute("hidden", true);
options.classList.toggle("hidden");

sel.addEventListener("click", (e) => {
    e.stopPropagation();
    // options.toggleAttribute("hidden");
    options.classList.toggle("hidden");
});

document.body.addEventListener("click", (e) => {
    // options.setAttribute("hidden", true);
    options.classList.add("hidden");
});

Array.from(options.children).forEach((option) => {
    if (option.classList.contains("selected")) option.classList.remove("selected");
    if (orderBy.includes(option.dataset.value)) {
        option.classList.add("selected");
    }
});

options.addEventListener("click", (e) => {
    if (e.target.tagName === "DIV") {
        e.stopPropagation();
        label.textContent = e.target.textContent;
        e.target.classList.add("selected");
        Array.from(e.target.parentNode.children).forEach((child) => {
            if (child !== e.target) {
                child.classList.remove("selected");
            }
        });
        // options.setAttribute("hidden", true);
        options.classList.add("hidden");

        // use it to fetch data
        // console.log(e.target.dataset.value);
        if (e.target.dataset.value === "default") {
            if (selector["orderby"]) delete selector["orderby"];
        } else {
            selector["orderby"] = "price " + e.target.dataset.value;
        }
        navigateTo(baseUrl, selector);
    }
});

// Set default value for selection
Array.from(options.children).forEach((item) => {
    if (item.classList.contains("selected")) {
        label.textContent = item.textContent;
    }
});
