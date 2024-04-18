const dropdownHeader = document.querySelector(".main-header__menu .dropdown-menu");
const dropdownUser = document.getElementById("dropdown-menu-user");
const dropdownButtonCollapse = document.getElementById("button-header-collapse");
const mainHeaderMechanism = document.getElementById("main-header-mechanism");
const mainHeaderMenu = document.getElementById("main-header-menu");

if (dropdownHeader) 
    dropdownHeader.addEventListener("click", (e) => e.stopPropagation());

if (dropdownUser) 
    dropdownUser.addEventListener("click", (e) => e.stopPropagation());

if (dropdownButtonCollapse)
    dropdownButtonCollapse.addEventListener("click", () => {
        mainHeaderMechanism.classList.toggle("hidden");
        mainHeaderMenu.classList.toggle("col-sm-2");
        mainHeaderMenu.classList.toggle("col-sm-12");
    });
