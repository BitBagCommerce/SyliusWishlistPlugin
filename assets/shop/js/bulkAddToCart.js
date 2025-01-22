import { BulkAction } from './bulkAction';

const bulkAddToCartButton = document.querySelectorAll('[data-wishlist-bulk-add-to-cart]');

const bulkAddToCart = () => {
    bulkAddToCartButton.forEach(btn => {
        btn.addEventListener('click', () => {
            new BulkAction(
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const form = document.querySelector(`#wishlist_form`);

                        const wishlistId = btn.dataset.wishlistBulkAddToCart;
                        const url = `${window.location.href}/products/add`;
                        const formData = new FormData(form);
                        const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf;

                        const headers = new Headers({
                            'X-CSRF-TOKEN': csrfToken
                        });

                        const requestConfig = {
                            method: 'POST',
                            headers: headers,
                            body: formData
                        }

                        try {
                            const response = await fetch(url, requestConfig);
                            const data = await response.json();

                        } catch (error) {
                            console.error(error);
                        } finally {
                            location.reload();
                        }
                    },
                }
            ).init();
        });
    })
};

const turnOnListener = () => {
    if (!bulkAddToCartButton) {
        return;
    }

    bulkAddToCart();
};

turnOnListener();
