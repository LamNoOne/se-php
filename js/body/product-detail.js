$(".product-desc-container").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    appendDots: $(".dots-append-container"),
    dots: true,
    dotsClass: "dots-append",
});

const allBtnDesc = document.querySelectorAll(".dots-append button");
const productDesc = ["About Product", "Details", "Specs"];

if(allBtnDesc.length === productDesc.length) {
    allBtnDesc.forEach((btn, index) => {
        const spanBlock = document.createElement("span");
        spanBlock.textContent = productDesc[index];
        btn.textContent = "";
        btn.appendChild(spanBlock);
    })
}
