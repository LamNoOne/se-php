$(".product-desc-container").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    appendDots: $(".dots-append-container"),
    dots: true,
    dotsClass: "dots-append",
});

const allBtnDesc = document.querySelectorAll(".dots-append button");
const productDesc = ["About Product", "Details", "Specs"];

const firstBtnDesc = document.querySelector(".dots-append button:nth-child(1)");

if (allBtnDesc.length === productDesc.length) {
    allBtnDesc.forEach((btn, index) => {
        const spanBlock = document.createElement("span");
        spanBlock.classList.add("span-nav");
        spanBlock.textContent = productDesc[index];
        btn.textContent = "";
        btn.appendChild(spanBlock);
    });
}

firstBtnDesc.querySelector("span").classList.add("active");

allBtnDesc.forEach((btn) => {
    btn.addEventListener("click", () => {
        // Find add active span => remove
        allBtnDesc.forEach((btn) => {
            if (btn.querySelector("span").classList.contains("active")) {
                btn.querySelector("span").classList.remove("active");
            }
        });

        // Config for this action element
        const spanBtn = btn.querySelector("span");
        if (!spanBtn.classList.contains("active")) {
            spanBtn.classList.add("active");
        }
    });
});
