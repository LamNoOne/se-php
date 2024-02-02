const dropdownHeader = document.querySelector(".main-header__menu .dropdown-menu");
const dropdownCart = document.getElementById("dropdown-menu-cart");
const dropdownUser = document.getElementById("dropdown-menu-user");
const dropdownButtonCollapse = document.getElementById("button-header-collapse");
const mainHeaderMechanism = document.getElementById("main-header-mechanism");
const mainHeaderMenu = document.getElementById("main-header-menu");

dropdownHeader.addEventListener("click", (e) => e.stopPropagation());

dropdownCart.addEventListener("click", (e) => e.stopPropagation());

dropdownUser.addEventListener("click", (e) => e.stopPropagation());

dropdownButtonCollapse.addEventListener("click", () => {
    mainHeaderMechanism.classList.toggle("hidden");
    mainHeaderMenu.classList.toggle("col-sm-2");
    mainHeaderMenu.classList.toggle("col-sm-12");
});
