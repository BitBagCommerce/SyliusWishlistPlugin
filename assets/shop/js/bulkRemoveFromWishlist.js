import { BulkAction } from './bulkAction';

const bulkRemoveFromWishlistButton = document.querySelectorAll('[data-wishlist-bulk-remove-from-wishlist]');
const bulkRemoveFromWishlist = () => {
    bulkRemoveFromWishlistButton.forEach(btn => {
        btn.addEventListener('click', () => {
            new BulkAction(
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const form = document.querySelector(`#wishlist_form`);

                        const wishlistId = btn.dataset.wishlistRemoveFromWishlist;
                        const url = `/en_US/wishlist/${wishlistId}/products/delete`;
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
                            console.log(data)

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
    if (!bulkRemoveFromWishlistButton) {
        return;
    }

    bulkRemoveFromWishlist();
};

turnOnListener();
