
const buttonGridLayout = document.querySelector(".grid-system");
const buttonGridHorizontal = document.querySelector(".grid-horizontal");

const handleButtonsGridLayout = (btn_first, btn_second) => {
    btn_first.addEventListener("click", () => {
        if(btn_first.classList.contains("blur")) {
            btn_first.classList.remove("blur");
            btn_second.classList.toggle("blur");
        }
    })
}


handleButtonsGridLayout(buttonGridLayout, buttonGridHorizontal);

handleButtonsGridLayout(buttonGridHorizontal, buttonGridLayout);