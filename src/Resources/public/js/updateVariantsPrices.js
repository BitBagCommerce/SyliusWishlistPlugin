const wishlistItem = document.querySelectorAll(".bitbag-wishlist-item");

wishlistItem.forEach((item) => {
    const dropdown = item.querySelector(".bitbag-wishlist-change-variant select");
    dropdown?.addEventListener("change", () => {
        const selectedVariant = dropdown.value;

        const selectedVariantData = item.querySelector(
            `[data-variant="${CSS.escape(selectedVariant)}"]`
        );

        const selectedVariantPrice = selectedVariantData.dataset.value;
        const productPrice = item.querySelector(".sylius-product-price");
        productPrice.innerHTML = selectedVariantPrice;
    });
});
